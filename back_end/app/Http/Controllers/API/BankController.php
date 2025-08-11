<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
require_once app_path('Helpers/Logger.php');


class BankController extends Controller
{

    public function index()
    {
        $banks = Bank::all();
        $perPage = request()->input('per_page', 15);
        $banks = Bank::paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $banks->items(),
            'meta' => [
                'current_page' => $banks->currentPage(),
                'per_page' => $banks->perPage(),
                'total' => $banks->total(),
                'last_page' => $banks->lastPage(),
                'from' => $banks->firstItem(),
                'to' => $banks->lastItem()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255|unique:banks",
        ]);

        $bank = Bank::create([
            "name" => $request->name,
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);

        log_action('Created ' . class_basename($bank) . ' "' . $request->name . '" ' . ' (ID: ' . $bank->id . ')');


        return response()->json(
            [
                "message" => "Bank created successfully",
                "bank" => $bank,
            ],
            201
        );
    }


    public function search(Request $request)
    {
        try {
            $query = Bank::query();

            // Basic search
            if ($request->has('q')) {
                $searchTerm = $request->input('q');
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }
            // Pagination
            $perPage = $request->input('per_page', 15);
            $banks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $banks->items(),
                'meta' => [
                    'current_page' => $banks->currentPage(),
                    'per_page' => $banks->perPage(),
                    'total' => $banks->total(),
                    'last_page' => $banks->lastPage(),
                    'from' => $banks->firstItem(),
                    'to' => $banks->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $bank = Bank::find($id);
            $bankName = $bank->name; // Store name before deletion

            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $bank->delete();
            log_action('Deleted  ' . class_basename($bank) . ' "' . $bankName . '" ' . ' (ID: ' . $id . ')');




            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Add other CRUD methods as needed
    // For example, update method can be added here
    public function update(Request $request, $id)
    {
        $bank = Bank::find($id);
        $bankName = $bank->name;

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => 'Bank not found'
            ], Response::HTTP_NOT_FOUND);
        }


        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $id,
        ], [
            'name.unique' => 'The bank name has already been taken.',
        ]);

        $bank->name = $request->name;
        $bank->updated_by = Auth::id();
        $bank->save();
        log_action('Updated  ' . $bankName . ' to ' . $request->name . ' ' . class_basename($bank) . ' (ID: ' . $id . ')');




        return response()->json([
            'success' => true,
            'message' => 'Bank updated successfully',
            'bank' => $bank
        ]);
    }

}




