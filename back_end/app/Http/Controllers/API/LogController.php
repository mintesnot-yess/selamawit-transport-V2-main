<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    /**
     * Display a listing of logs.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $query = Log::with("user")->latest();

        // Add filters if needed
        if ($request->has("action")) {
            $query->where("action", "like", "%" . $request->action . "%");
        }

        if ($request->has("user_id")) {
            $query->where("user_id", $request->user_id);
        }

        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem()
            ]
        ]);
    }

    /**
     * Store a new log entry.
     * Note: Typically logs are created automatically, not via API
     */
    public function store(Request $request)
    {
        $request->validate([
            "action" => "required|string|max:255",
            "ip_address" => "nullable|ip",
        ]);

        $log = Log::create([
            "user_id" => Auth::id(),
            "action" => $request->action,
            "ip_address" => $request->ip_address ?? request()->ip(),
        ]);

        return response()->json(
            [
                "message" => "Log entry created",
                "data" => $log,
            ],
            201
        );
    }

    /**
     * Display the specified log.
     */
    public function show($id)
    {
        $log = Log::with("user")->findOrFail($id);
        return response()->json(["data" => $log]);
    }

    /**
     * Get current user's logs
     */
    public function myLogs()
    {
        $logs = Log::where("user_id", Auth::id())->latest()->paginate(15);

        return response()->json(["data" => $logs]);
    }
}
