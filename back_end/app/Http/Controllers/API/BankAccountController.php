<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// import log_action from app/Helpers/Logger.php
require_once app_path("Helpers/Logger.php");

class BankAccountController extends Controller
{
    // fetch all bank accounts
    // for a specific bank by bank id

    public function show(Request $request, $id)
    {
        $bankAccounts = BankAccount::with("bank:id,name")
            ->where("bank_id", $id)
            ->orderBy("created_at", "desc")
            ->paginate(request()->input("per_page", 15))
            ->through(function ($account) {
                return [
                    "id" => $account->id,
                    "account_number" => $account->account_number,
                    "bank" => [
                        "id" => $account->bank->id,
                        "name" => $account->bank->name,
                    ],
                    "created_at" => $account->created_at,
                    "updated_at" => $account->updated_at,
                ];
            });

        $response = [
            "success" => true,
            "data" => $bankAccounts->items(),
            "meta" => [
                "current_page" => $bankAccounts->currentPage(),
                "per_page" => $bankAccounts->perPage(),
                "total" => $bankAccounts->total(),
                "last_page" => $bankAccounts->lastPage(),
                "from" => $bankAccounts->firstItem(),
                "to" => $bankAccounts->lastItem(),
            ],
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            "account_number" => "required|min:3|max:255|unique:bank_accounts",
            "bank_id" => "required",
        ]);

        $bankAccount = BankAccount::create([
            "account_number" => $request->input("account_number"),
            "bank_id" => $request->input("bank_id"),
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);

        // log_action

        log_action(
            "Created " . class_basename($bankAccount) . " #" . $bankAccount->id,
        );

        return response()->json(
            [
                "message" => "Bank account created successfully",
                "bank_account" => $bankAccount,
            ],
            201,
        );
    }

    // Add destroy method
    public function destroy($id)
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(
                [
                    "message" => "Bank account not found",
                ],
                404,
            );
        }

        $bankAccount->delete();
        log_action(
            "Deleted " . class_basename($bankAccount) . " #" . $bankAccount->id,
        );
        return response()->json([
            "message" => "Bank account deleted successfully",
        ]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "account_number" =>
                "required|string|max:255|unique:bank_accounts,account_number," .
                $id,
            "bank_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(
                [
                    "message" => "Bank account not found",
                ],
                404,
            );
        }

        $bankAccount->update([
            "account_number" => $request->account_number,
            "bank_id" => $request->bank_id,
            "updated_by" => Auth::id(),
        ]);
        log_action(
            "Updated " . class_basename($bankAccount) . " #" . $bankAccount->id,
        );

        return response()->json(
            [
                "message" => "Bank account updated successfully",
                "bank_account" => $bankAccount,
            ],
            200,
        );
    }

    public function search(Request $request, $id)
    {
        $query = BankAccount::with("bank:id,name")->where("bank_id", $id);

        if ($request->has("account_number")) {
            $query->where(
                "account_number",
                "like",
                "%" . $request->account_number . "%",
            );
        }

        $bankAccounts = $query
            ->orderBy("created_at", "desc")
            ->get()
            ->map(function ($account) {
                return [
                    "id" => $account->id,
                    "account_number" => $account->account_number,
                    "bank" => [
                        "id" => $account->bank->id,
                        "name" => $account->bank->name,
                    ],
                    "created_at" => $account->created_at,
                    "updated_at" => $account->updated_at,
                ];
            });

        return response()->json($bankAccounts);
    }
}
