<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index()
    {
        $perPage = request()->input("per_page", 15);
        $vehicles = Vehicle::paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $vehicles->items(),
            "meta" => [
                "current_page" => $vehicles->currentPage(),
                "per_page" => $vehicles->perPage(),
                "total" => $vehicles->total(),
                "last_page" => $vehicles->lastPage(),
                "from" => $vehicles->firstItem(),
                "to" => $vehicles->lastItem(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "plate_number" => "required|string|unique:vehicles",
            "vehicle_name" => "required|string|max:255",
            "owner_name" => "required|string|max:255",
            "owner_phone" => "required|string|max:20",
            "owner_type" => ["required", Rule::in(["OWNED", "PRIVATE"])],
            "libre" => "required|max:2048",
        ]);

        $librePath = null;
        if ($request->hasFile("libre")) {
            $librePath = $request->file("libre")->store("libres", "public");
        }

        $vehicle = Vehicle::create([
            "plate_number" => $request->plate_number,
            "vehicle_name" => $request->vehicle_name,
            "owner_name" => $request->owner_name,
            "owner_phone" => $request->owner_phone,
            "owner_type" => $request->owner_type,
            "libre" => $librePath,
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);

        return response()->json(
            [
                "message" => "Vehicle created successfully",
                "vehicle" => $vehicle,
            ],
            201
        );
    }

    // Add other CRUD methods (index, show, update, destroy) as needed
    public function show($id)
    {
        $vehicle = Vehicle::with(['expense'])->find($id);
        $expenses = Expense::with([
            "expenseType",
            "vehicle",
            "employee",
            "fromBank",
            "toBank",
            "fromAccount",
        ])
            ->where("vehicle_id", $id)
            ->get();
        if (!$vehicle) {
            return response()->json(
                [
                    "message" => "Vehicle not found",
                ],
                404
            );
        }

        return response()->json([
            "success" => true,
            "vehicle" => $vehicle,
            "expenses" => $expenses,

        ]);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json(
                [
                    "message" => "Vehicle not found",
                ],
                404
            );
        }

        $request->validate([
            "plate_number" => [
                "required",
                "string",
                Rule::unique("vehicles")->ignore($vehicle->id),
            ],
            "vehicle_name" => "required|string|max:255",
            "owner_name" => "required|string|max:255",
            "owner_phone" => "required|string|max:20",
            "owner_type" => ["required", Rule::in(["OWNED", "PRIVATE"])],
            "libre" => "nullable|max:2048",
        ]);



        if ($request->hasFile("libre")) {
            $librePath = $request->file("libre")->store("libres", "public");
            $vehicle->libre = $librePath;
        }

        $vehicle->plate_number = $request->plate_number;
        $vehicle->vehicle_name = $request->vehicle_name;
        $vehicle->owner_name = $request->owner_name;
        $vehicle->owner_phone = $request->owner_phone;
        $vehicle->owner_type = $request->owner_type;
        $vehicle->updated_by = Auth::id();
        $vehicle->save();

        return response()->json([
            "message" => "Vehicle updated successfully",
            "vehicle" => $vehicle,
        ]);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json(
                [
                    "message" => "Vehicle not found",
                ],
                404
            );
        }

        $vehicle->delete();

        return response()->json([
            "message" => "Vehicle deleted successfully",
        ]);
    }
}
