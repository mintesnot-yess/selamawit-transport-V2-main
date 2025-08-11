<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator as IlluminateValidator;
use Illuminate\Support\Facades\Validator;
require_once app_path("Helpers/Logger.php");

class AuthController extends Controller
{
    public function index()
    {

        $perPage = request()->input("per_page", 15);
        $users = User::with("role")->paginate($perPage);
        $roles = Role::all();

        return response()->json([
            "success" => true,
            "data" => $users->items(),
            "roles" => $roles,
            "meta" => [
                "current_page" => $users->currentPage(),
                "per_page" => $users->perPage(),
                "total" => $users->total(),
                "last_page" => $users->lastPage(),
                "from" => $users->firstItem(),
                "to" => $users->lastItem(),
            ],
        ]);
    }
    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
            "role" => "exists:roles,id",
        ]);

        // Create the user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);
        // log_action('Created ' . class_basename($user), Auth::id());

        // Assign role
        $user->roles()->attach($request->role, [
            "user_type" => "App\Models\User", // Or whatever your user class is
        ]);

        return response()->json(
            [
                "message" => "User registered successfully",
                "user" => $user->load("roles"), // Eager load roles if needed
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    "message" => "User not found",
                ],
                404
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    "message" => "Invalid credentials",
                ],
                401
            );
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        // log_action('Login ' . class_basename($user) . ' ' . $user->name . ' | ' . $user->email, $user->id);

        return response()->json([
            "access_token" => $token,
            "token_type" => "Bearer",
            "user" => $user,
        ]);
    }
    // update
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            "name" => "sometimes|required|string|max:255",
            "email" => "sometimes|required|string|email|max:255|unique:users,email,$id",
            "role" => "sometimes|exists:roles,id"
        ]);

        // Update basic fields
        $user->fill($request->only(['name', 'email']));
        $user->updated_by = Auth::id();
        $user->save();

        // Handle role assignment
        if ($request->has('role')) {
            // Use sync() instead of attach() to avoid duplicate relations
            $user->roles()->sync([
                $request->role => [
                    'user_type' => get_class($user),
                    'updated_by' => Auth::id()
                ]
            ]);
        }


        log_action(
            "Update " . class_basename($user) . " " .
            $user->name . " | " . $user->email . " " . $user->id
        );

        return response()->json([
            "message" => "User updated successfully",
            "user" => $user->load('roles')
        ]);
    }
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            "message" => "User deleted successfully",
        ]);
    }

    public function bulkDelete(Request $request)
    {
        // Validate input
        $request->validate([
            "ids" => "required|array",
            "ids.*" => "integer|exists:users,id",
        ]);

        $ids = $request->input("ids"); // Already decoded by Laravel

        User::whereIn("id", $ids)->delete();

        return response()->json([
            "success" => true,
            "message" => "Selected users deleted successfully",
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Logged out successfully",
        ]);
    }
}
