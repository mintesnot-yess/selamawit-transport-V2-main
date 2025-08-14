<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\BankAccountController;
use App\Http\Controllers\API\ClientsController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ExpenseTypeController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\LoadTypeController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\IncomeController;
use App\Http\Controllers\API\LogController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("dashboard", [DashboardController::class, "index"]);
    Route::post("/register", [AuthController::class, "register"]);

    Route::get("/users", [AuthController::class, "index"]);
    Route::delete("/users/bulk", [AuthController::class, "bulkDelete"]);
    Route::delete("/user", [AuthController::class, "destroy"]);
    Route::put("/users/{id}", [AuthController::class, "update"]);
    Route::delete("/users/{id}", [AuthController::class, "destroy"]);
    Route::get("/users/{id}", [AuthController::class, "show"]);

    Route::get("/user", function (Request $request) {
        $user = $request->user();
        $user->load("roles");
        $permissions = $user->allPermissions()->pluck("name")->toArray();
        return response()->json([
            "user" => $user,
            "roles" => $user->roles->pluck("name"),
            "permissions" => $permissions,
        ]);
    });

    Route::post("/logout", [AuthController::class, "logout"]);

    // Bank routes
    Route::resource("banks", BankController::class);
    Route::resource("bank-accounts", BankAccountController::class);

    // Vehicle routes
    Route::resource("vehicles", VehicleController::class);

    // Client routes
    Route::resource("clients", ClientsController::class);

    // Employee routes
    Route::resource("employees", EmployeeController::class);

    // Expense Type routes
    Route::resource("expense-types", ExpenseTypeController::class);

    // Expense routes
    Route::resource("expenses", ExpenseController::class);

    // Load Type routes
    Route::resource("load-types", LoadTypeController::class);

    // Location routes
    Route::resource("locations", LocationController::class);

    // Order routes
    Route::resource("orders", OrderController::class);

    // Income routes
    Route::resource("incomes", IncomeController::class);

    // Log routes
    Route::resource("logs", LogController::class)->only(['index', 'show']);

    // Role routes
    Route::resource("roles", RoleController::class);
    Route::post("roles/{role}/permissions", [RoleController::class, "updatePermissions"]);
    Route::get("roles/{role}/permissions", [RoleController::class, "getPermissions"]);
    Route::match(["get", "post"], "roles/{role}/permissions", [RoleController::class, "handlePermissions"]);

    // Permission routes
    Route::resource("permissions", PermissionController::class);

    // Report routes
    Route::prefix("reports")->group(function () {
        Route::get("/own-summary", [ReportsController::class, "getOwnReport"]);
        Route::get("/income", [ReportsController::class, "getIncomeReport"]);
        Route::get("/expense", [ReportsController::class, "getExpenseReport"]);
        Route::get("/order", [ReportsController::class, "getOrderReport"]);
        Route::get("/vehicle", [ReportsController::class, "getVehicleReport"]);
        Route::get("/employee", [ReportsController::class, "getEmployeeReport"]);
    });
});
