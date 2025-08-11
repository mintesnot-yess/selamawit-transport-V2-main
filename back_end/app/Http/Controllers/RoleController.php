<?php


namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Response;
use DB;


require_once app_path('Helpers/Logger.php');


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $perPage = request()->input('per_page', 15);
        $roles = Role::with('permissions')->paginate($perPage);
        $permission = Permission::all();

        return response()->json([
            'success' => true,
            'data' => $roles->items(),
            'permissions' => $permission,
            'meta' => [
                'current_page' => $roles->currentPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'last_page' => $roles->lastPage(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validation
        $request->validate([
            "name" => "required|string|max:255|unique:roles",
        ], [
            'name.unique' => 'The role name has already been taken.',
        ]);

        // SQLSTATE[HY000]: General error: 1364 Field 'created_by' doesn't have a default value (Connection: mysql, SQL: insert into `roles` (`name`, `display_name`, `updated_at`, `created_at`) values (Addis Bank, Addis Bank, 2025-06-06 20:38:38, 2025-06-06 20:38:38))

        $role = Role::create([
            "name" => $request->name,
            "display_name" => ucfirst($request->name),
            // "created_by" => Auth::id(),
            // "updated_by" => Auth::id()
        ]);
        log_action('Created ' . class_basename($role) . ' #' . $role->id);

        return response()->json(
            [
                "message" => "Role created successfully",
                "role" => $role,
            ],
            201
        );
    }


    public function updatePermissions(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Ensure permissions is always treated as an array
            $permissions = $request->input('permissions', []);
            if (!is_array($permissions)) {
                $permissions = [$permissions];
            }

            $validated = $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            DB::beginTransaction();

            // Convert all permission IDs to integers to ensure proper sync
            $permissionIds = array_map('intval', $validated['permissions']);
            $role->permissions()->sync($permissionIds);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully',
                'data' => [
                    'role' => $role->only(['id', 'name']),
                    'permissions' => $role->permissions()->select('id', 'name')->get()
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Permission sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function handlePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($request->isMethod('get')) {
            return response()->json([
                'permissions' => $role->permissions
            ]);
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            $role->permissions()->sync($request->permissions);
            return response()->json(['success' => true]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(["message" => "Role not found"], 404);
        }

        $validator = \Validator::make($request->all(), [
            "name" => "sometimes|required|string|max:255|unique:roles,name," . $id,
        ], [
            'name.unique' => 'The role name has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "message" => "Validation error",
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $role->fill($request->only([
            "name",
        ]));
        $role->updated_by = Auth::id();
        $role->save();

        log_action('Updated ' . class_basename($role) . ' #' . $role->id);

        return response()->json([
            "message" => "Role updated successfully",
            "role" => $role,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

    //  create search function
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $perPage = $request->input('per_page', 15);

        $roles = Role::where('name', 'like', '%' . $query . '%')
            ->orWhere('display_name', 'like', '%' . $query . '%')
            ->with('permissions')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'last_page' => $roles->lastPage(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem()
            ]
        ]);
    }
    public function destroy($id)
    {
        try {
            $bank = Role::find($id);

            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $bank->delete();
            log_action('Deleted ' . class_basename($bank) . ' #' . $bank->id);


            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}








