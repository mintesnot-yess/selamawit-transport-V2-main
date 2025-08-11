<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $perPage = request()->input('per_page', 15);
        $expenseTypes = ExpenseType::paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $expenseTypes->items(),
            'meta' => [
                'current_page' => $expenseTypes->currentPage(),
                'per_page' => $expenseTypes->perPage(),
                'total' => $expenseTypes->total(),
                'last_page' => $expenseTypes->lastPage(),
                'from' => $expenseTypes->firstItem(),
                'to' => $expenseTypes->lastItem()
            ]
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:PRIVATE,OWN,GENERAL,VEHICLE,EMPLOYEE',
        ]);

        $expenseType = ExpenseType::create([
            'name' => $request->name,
            'category' => $request->category,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $expenseType,
            'message' => 'Expense type created successfully.'
        ], 201);
    }






    // Add show(), update(), destroy() methods similarly
    public function show($id)
    {
        $expenseType = ExpenseType::find($id);

        if (!$expenseType) {
            return response()->json(['success' => false, 'message' => 'Expense type not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $expenseType]);
    }

    public function update(Request $request, $id)
    {
        $expenseType = ExpenseType::find($id);

        if (!$expenseType) {
            return response()->json(['success' => false, 'message' => 'Expense type not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:PRIVATE,OWN,GENERAL,VEHICLE,EMPLOYEE',
            // Add other fields as needed
        ]);

        $expenseType->update($validated);

        return response()->json(['success' => true, 'data' => $expenseType]);
    }

    public function destroy($id)
    {
        $expenseType = ExpenseType::find($id);

        if (!$expenseType) {
            return response()->json(['success' => false, 'message' => 'Expense type not found.'], 404);
        }

        $expenseType->delete();

        return response()->json(['success' => true, 'message' => 'Expense type deleted successfully.']);
    }
    public function search(Request $request)
    {
        try {
            $query = ExpenseType::query();

            // Basic search
            if ($request->has('q')) {
                $searchTerm = $request->input('q');
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            // Pagination
            $perPage = $request->input('per_page', 15);
            $expenseTypes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $expenseTypes->items(),
                'meta' => [
                    'current_page' => $expenseTypes->currentPage(),
                    'per_page' => $expenseTypes->perPage(),
                    'total' => $expenseTypes->total(),
                    'last_page' => $expenseTypes->lastPage(),
                    'from' => $expenseTypes->firstItem(),
                    'to' => $expenseTypes->lastItem()
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

}
