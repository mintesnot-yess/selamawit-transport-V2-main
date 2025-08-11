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
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("dashboard", [DashboardController::class, "index"]);

    Route::get("/users", [AuthController::class, "index"]);
    Route::delete("/users/bulk", [AuthController::class, "bulkDelete"]);

    Route::delete("/user", [AuthController::class, "destroy"]);
    Route::put("/users/{id}", [AuthController::class, "update"]);
    // delete
    Route::delete("/users/{id}", [AuthController::class, "destroy"]);

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

    // Route::resource("vehicles", VehicleController::class);

    // Vehicle routes
    Route::prefix("vehicles")->group(function () {
        Route::get("/", [VehicleController::class, "index"]);
        Route::get("/{id}", [VehicleController::class, "show"]);
        Route::post("/", [VehicleController::class, "store"]);
        Route::get("/search", [VehicleController::class, "search"]);
        Route::delete("/{id}", [VehicleController::class, "destroy"]);
        Route::put("/{id}", [VehicleController::class, "update"]);
        // Add other vehicle routes as needed
    });

    // Customer routes
    Route::prefix("clients")->group(function () {
        Route::get("/", [ClientsController::class, "index"]);
        Route::post("/", [ClientsController::class, "store"]);
        Route::get("/search", [ClientsController::class, "search"]);
        Route::delete("/{id}", [ClientsController::class, "destroy"]);
        Route::put("/{id}", [ClientsController::class, "update"]);
        // Add other client routes as needed
    });

    // Employee routes
    Route::prefix("employees")->group(function () {
        Route::get("/", [EmployeeController::class, "index"]);
        Route::post("/", [EmployeeController::class, "store"]);
        Route::delete("/{id}", [EmployeeController::class, "destroy"]);
        Route::put("/{id}", [EmployeeController::class, "update"]);
        // Add other employee routes as needed
    });

    // Expense Type routes
    Route::prefix("expense-types")->group(function () {
        Route::get("/", [ExpenseTypeController::class, "index"]);
        Route::post("/", [ExpenseTypeController::class, "store"]);
        Route::get("/search", [ExpenseTypeController::class, "search"]);
        Route::delete("/{id}", [ExpenseTypeController::class, "destroy"]);
        Route::put("/{id}", [ExpenseTypeController::class, "update"]);
        // Add other expense type routes as needed
    });

    // Expense routes
    Route::prefix("expenses")->group(function () {
        Route::get("/", [ExpenseController::class, "index"]);
        Route::post("/", [ExpenseController::class, "store"]);
        Route::get("/search", [ExpenseController::class, "search"]);
        Route::delete("/{id}", [ExpenseController::class, "destroy"]);
        Route::put("/{id}", [ExpenseController::class, "update"]);
        // Add other expense routes as needed
    });

    // Load Type crud routes
    Route::prefix("load-types")->group(function () {
        Route::get("/", [LoadTypeController::class, "index"]);
        Route::post("/", [LoadTypeController::class, "store"]);
        Route::get("/search", [LoadTypeController::class, "search"]);
        Route::get("/{id}", [LoadTypeController::class, "show"]);
        Route::put("/{id}", [LoadTypeController::class, "update"]);
        Route::delete("/{id}", [LoadTypeController::class, "destroy"]);
    });

    // Location crud routes
    Route::prefix("locations")->group(function () {
        Route::get("/", [LocationController::class, "index"]);
        Route::post("/", [LocationController::class, "store"]);
        Route::get("/search", [LocationController::class, "search"]);
        Route::get("/{id}", [LocationController::class, "show"]);
        Route::put("/{id}", [LocationController::class, "update"]);
        Route::delete("/{id}", [LocationController::class, "destroy"]);
    });

    // Order routes
    Route::prefix("orders")->group(function () {
        Route::get("/", [OrderController::class, "index"]);
        Route::post("/", [OrderController::class, "store"]);
        Route::get("/search", [OrderController::class, "search"]);
        Route::get("/{id}", [OrderController::class, "show"]);
        Route::put("/{id}", [OrderController::class, "update"]);
        Route::delete("/{id}", [OrderController::class, "destroy"]);
    }); // Income routes

    Route::prefix("incomes")->group(function () {
        Route::get("/", [IncomeController::class, "index"]);
        Route::post("/", [IncomeController::class, "store"]);
        Route::get("/search", [IncomeController::class, "search"]);
        Route::get("/{id}", [IncomeController::class, "show"]);
        Route::put("/{id}", [IncomeController::class, "update"]);
        Route::delete("/{id}", [IncomeController::class, "destroy"]);
    });

    // Income routes
    Route::prefix("incomes")->group(function () {
        Route::apiResource("/", IncomeController::class);
    });

    // Log routes
    Route::prefix("logs")->group(function () {
        Route::get("/", [LogController::class, "index"]);
        Route::get("/{log}", [LogController::class, "show"]);
    });

    // Ensure only authorized users (e.g., admins) can create roles
    Route::prefix("roles")->group(function () {
        Route::get("/", [RoleController::class, "index"]);
        Route::post("/", [RoleController::class, "store"]);
        Route::get("/search", [RoleController::class, "search"]);
        Route::get("/{role}", [RoleController::class, "show"]);
        Route::put("/{role}", [RoleController::class, "update"]);

        Route::delete("/{role}", [RoleController::class, "destroy"]);

        Route::post("/{role}/permissions", [
            RoleController::class,
            "updatePermissions",
        ]); // Existing POST endpoint
        Route::get("/{role}/permissions", [
            RoleController::class,
            "getPermissions",
        ]); // New GET endpoint
        Route::match(["get", "post"], "/{role}/permissions", [
            RoleController::class,
            "handlePermissions",
        ]);
    });

    Route::prefix("permissions")->group(function () {
        Route::get("/", [PermissionController::class, "index"]);
        Route::post("/", [PermissionController::class, "store"]);
        Route::get("/{role}", [PermissionController::class, "show"]);
        Route::get("/search", [PermissionController::class, "search"]);
        Route::put("/{role}", [PermissionController::class, "update"]);
        Route::delete("/{role}", [PermissionController::class, "destroy"]);
    });
});
