<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Income;
use App\Models\Expense;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
require_once app_path("Helpers/Logger.php");

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $vehicleCount = Vehicle::count();
        $employeeCount = Employee::count();
        $orderCount = Order::count();
        $year = now()->year;

        $query = Order::with([
            "client",
            "employee",
            "vehicle",
            "loadType",
            "loadingLocation",
            "destinationLocation",
        ])->limit(3);
        $orders = $query->get();

        // Chart Data
        $incomeData = Income::whereYear("created_at", $year)
            ->selectRaw("MONTH(created_at) as month, SUM(amount) as total")
            ->groupBy("month")
            ->pluck("total", "month")
            ->toArray();

        $expenseData = Expense::whereYear("created_at", $year)
            ->selectRaw("MONTH(created_at) as month, SUM(amount) as total")
            ->groupBy("month")
            ->pluck("total", "month")
            ->toArray();

        $monthNumbers = range(1, 12);
        $monthLabels = array_map(
            fn($m) => \Carbon\Carbon::create()->month($m)->format("M"),
            $monthNumbers,
        );

        $income = [];
        $expenses = [];

        foreach ($monthNumbers as $monthNumber) {
            $income[] = $incomeData[$monthNumber] ?? 0;
            $expenses[] = $expenseData[$monthNumber] ?? 0;
        }

        return response()->json([
            "success" => true,
            "counts" => [
                "users" => $userCount,
                "vehicles" => $vehicleCount,
                "employees" => $employeeCount,
                "orders" => $orderCount,
            ],
            "orders" => $orders,
            "chart" => [
                "months" => $monthLabels,
                "income" => $income,
                "expenses" => $expenses,
            ],
            "totalIncome" => Income::sum("amount"),
            "totalExpense" => Expense::sum("amount"),
        ]);
    }
}
