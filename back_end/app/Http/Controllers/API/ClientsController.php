<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
require_once app_path("Helpers/Logger.php");

class ClientsController extends Controller
{
    // Add other CRUD methods as needed
    public function index()
    {
        $clients = Client::all();
        $perPage = request()->input("per_page", 15);
        $clients = Client::paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $clients->items(),
            "meta" => [
                "current_page" => $clients->currentPage(),
                "per_page" => $clients->perPage(),
                "total" => $clients->total(),
                "last_page" => $clients->lastPage(),
                "from" => $clients->firstItem(),
                "to" => $clients->lastItem(),
            ],
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "contact_person" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "address" => "required|string",
        ]);

        $client = Client::create([
            "name" => $request->name,
            "contact_person" => $request->contact_person,
            "phone" => $request->phone,
            "address" => $request->address,
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);
        log_action("Created " . class_basename($client) . " #" . $client->id);

        return response()->json(
            [
                "message" => "Client created successfully",
                "customer" => $client,
            ],
            201
        );
    }
    public function show($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(["message" => "Client not found"], 404);
        }
        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        // Implement update logic
        $client = Client::find($id);
        if (!$client) {
            return response()->json(["message" => "Client not found"], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "sometimes|required|string|max:255",
            "contact_person" => "sometimes|required|string|max:255",
            "phone" => "sometimes|required|string|max:20",
            "address" => "sometimes|required|string",
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

        $client->fill(
            $request->only(["name", "contact_person", "phone", "address"])
        );
        $client->updated_by = Auth::id();
        $client->save();

        log_action("Updated " . class_basename($client) . " #" . $client->id);

        return response()->json([
            "message" => "Client updated successfully",
            "customer" => $client,
        ]);
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(["message" => "Client not found"], 404);
        }

        $client->delete();

        log_action("Deleted " . class_basename($client) . " #" . $client->id);

        return response()->json([
            "message" => "Client deleted successfully",
        ]);
    }
    public function search(Request $request)
    {
        $perPage = $request->input("per_page", 15);

        $query = Client::query();

        $query->where("name", "like", "%" . $request->input("q") . "%");

        // if ($request->filled('contact_person')) {
        //     $query->where('contact_person', 'like', '%' . $request->input('q') . '%');
        // }
        // if ($request->filled('phone')) {
        //     $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        // }
        // if ($request->filled('address')) {
        //     $query->where('address', 'like', '%' . $request->input('address') . '%');
        // }

        $clients = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $clients->items(),
            "meta" => [
                "current_page" => $clients->currentPage(),
                "per_page" => $clients->perPage(),
                "total" => $clients->total(),
                "last_page" => $clients->lastPage(),
                "from" => $clients->firstItem(),
                "to" => $clients->lastItem(),
            ],
        ]);
    }
}
