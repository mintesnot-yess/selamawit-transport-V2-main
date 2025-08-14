<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function getOwnReport()
    {
        // Your logic to generate own report
        $orders = Order::with([
            "client",
            "employee",
            "vehicle",
            "loadType",
            "loadingLocation",
            "destinationLocation",
            "expense",
            "income",
        ])->orderBy('created_at', 'desc')->get();


        return response()->json([
            'data' => [
                'orders' => $orders,
            ]

        ]);
    }
    public function getExpenseReport(Request $request)
    {
        // Your logic to generate expense report
        return response()->json(['message' => 'Expense report data']);
    }

    public function getOrderReport(Request $request)
    {
        // Your logic to generate order report
        return response()->json(['message' => 'Order report data']);
    }

    public function getVehicleReport(Request $request)
    {
        // Your logic to generate vehicle report
        return response()->json(['message' => 'Vehicle report data']);
    }

    public function getEmployeeReport(Request $request)
    {
        // Your logic to generate employee report
        return response()->json(['message' => 'Employee report data']);
    }
}
