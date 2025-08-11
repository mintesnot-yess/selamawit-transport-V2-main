<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Models\Income;
use App\Models\LoadType;
use App\Models\Location;
use App\Models\Order;
use App\Models\Expense;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $perPage = request()->input("per_page", 15);

        // Eager load related models
        $query = Order::with([
            "client",
            "employee",
            "vehicle",
            "loadType",
            "loadingLocation",
            "destinationLocation",
        ]);

        $clients = Client::all();
        $employees = Employee::all();
        $Vehicles = Vehicle::all();
        $location = Location::all();
        $loadTypes = LoadType::all();

        $orders = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $orders->items(),
            "clients" => $clients,
            "employees" => $employees,
            "vehicles" => $Vehicles,
            "locations" => $location,
            "loadTypes" => $loadTypes,

            "meta" => [
                "current_page" => $orders->currentPage(),
                "per_page" => $orders->perPage(),
                "total" => $orders->total(),
                "last_page" => $orders->lastPage(),
                "from" => $orders->firstItem(),
                "to" => $orders->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "client_id" => "required|exists:clients,id",
            "employee_id" => "required|exists:employees,id",
            "vehicle_id" => "required|exists:vehicles,id",
            "loading_place" => "required|exists:locations,id",
            "destination" => "required|exists:locations,id",
            "load_type_id" => "required|exists:load_types,id",
            "quintal" => "required|numeric|min:0",
            "given_tariff" => "required|numeric|min:0",
            "sub_tariff" => "required|numeric|min:0",
            "arrival_at_loading_site" => "required|date",
            "loading_date" =>
                "required|date|after_or_equal:arrival_at_loading_site",
        ]);

        $order = Order::create(
            attributes: [
                "client_id" => $request->client_id,
                "employee_id" => $request->employee_id,
                "vehicle_id" => $request->vehicle_id,
                "loading_place" => $request->loading_place,
                "destination" => $request->destination,
                "load_type_id" => $request->load_type_id,
                "quintal" => $request->quintal,
                "given_tariff" => $request->given_tariff,
                "sub_tariff" => $request->sub_tariff,
                "order_date" => now(),
                "arrival_at_loading_site" => $request->arrival_at_loading_site,
                "loading_date" => $request->loading_date,
                "current_condition" => "LOADED",
                "payment_collected" => false,
                "status" => "PENDING",
                "created_by" => Auth::id(),
                "updated_by" => Auth::id(),
            ]
        );

        return response()->json(
            [
                "message" => "Order created successfully",
                "data" => $order,
            ],
            201
        );
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with([
            "client",
            "employee",
            "vehicle",
            "loadType",
            "loadingLocation",
            "destinationLocation",
            "expense",
            "income",
        ])->find($id);

        $expense_type = ExpenseType::all();
        $expenses = Expense::with([
            "expenseType",
            "vehicle",
            "employee",
            "fromBank",
            "toBank",
            "fromAccount",
        ])
            ->where("order_id", $id)
            ->get();


        $income = Income::with(["bank"])->where("order_id", $id)
            ->get();

        $bank = Bank::all();
        $bank_account = BankAccount::all();

        if (!$order) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Order not found",
                ],
                404
            );
        }

        return response()->json([
            "success" => true,
            "order" => $order,
            "bank" => $bank,
            "bank_account" => $bank_account,
            "income" => $income,
            "expense_type" => $expense_type,
            "expenses" => $expenses,
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(
                [
                    "message" => "Order not found",
                ],
                404
            );
        }

        $data = $request->validate([
            "client_id" => "required|exists:clients,id",
            "employee_id" => "required|exists:employees,id",
            "vehicle_id" => "required|exists:vehicles,id",
            "loading_place" => "required|exists:locations,id",
            "destination" => "required|exists:locations,id",
            "load_type_id" => "required|exists:load_types,id",
            "quintal" => "required|numeric|min:0",
            "given_tariff" => "required|numeric|min:0",
            "sub_tariff" => "required|numeric|min:0",
            "arrival_at_loading_site" => "required|date",
            "loading_date" =>
                "required|date|after_or_equal:arrival_at_loading_site",
        ]);

        $order->update([
            "client_id" => $request->client_id,
            "employee_id" => $request->employee_id,
            "vehicle_id" => $request->vehicle_id,
            "loading_place" => $request->loading_place,
            "destination" => $request->destination,
            "load_type_id" => $request->load_type_id,
            "quintal" => $request->quintal,
            "given_tariff" => $request->given_tariff,
            "sub_tariff" => $request->sub_tariff,
            "arrival_at_loading_site" => $request->arrival_at_loading_site,
            "loading_date" => $request->loading_date,
            "updated_by" => Auth::id(),
        ]);

        return response()->json([
            "message" => "Order updated successfully",
            "data" => $order,
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(
                [
                    "message" => "Order not found",
                ],
                404
            );
        }

        // Check if order has associated incomes
        if ($order->incomes()->count() > 0) {
            return response()->json(
                [
                    "message" =>
                        "Cannot delete order as it has associated incomes",
                ],
                400
            );
        }

        $order->delete();

        return response()->json(
            [
                "message" => "Order deleted successfully",
            ],
            204
        );
    }
}
