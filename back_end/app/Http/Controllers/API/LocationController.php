<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index()
    {
        $perPage = request()->input("per_page", 15);
        $locations = Location::paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $locations->items(),
            "meta" => [
                "current_page" => $locations->currentPage(),
                "per_page" => $locations->perPage(),
                "total" => $locations->total(),
                "last_page" => $locations->lastPage(),
                "from" => $locations->firstItem(),
                "to" => $locations->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $request->validate([
            "location_name" => "required|string|max:255|unique:locations",
        ]);

        $location = Location::create([
            "location_name" => $request->location_name,
            "created_by" => Auth::id(),
            "updated_by" => Auth::id(),
        ]);

        return response()->json(
            [
                "message" => "Location created successfully",
                "data" => $location,
            ],
            201
        );
    }

    /**
     * Display the specified location.
     */
    public function show($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(
                [
                    "message" => "Location not found",
                ],
                404
            );
        }

        return response()->json(["data" => $location]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, $id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(
                [
                    "message" => "Location not found",
                ],
                404
            );
        }

        $validator = Validator::make($request->all(), [
            "location_name" =>
                "required|string|max:255|unique:locations,location_name," . $id,
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

        $location->update([
            "location_name" => $request->location_name,
            "updated_by" => Auth::id(),
        ]);

        return response()->json([
            "message" => "Location updated successfully",
            "data" => $location,
        ]);
    }

    /**
     * Remove the specified location.
     */
    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(
                [
                    "message" => "Location not found",
                ],
                404
            );
        }

        // Check if location is used in any orders
        if (
            $location->loadingOrders()->count() > 0 ||
            $location->destinationOrders()->count() > 0
        ) {
            return response()->json(
                [
                    "message" =>
                        "Cannot delete location as it is being used in orders",
                ],
                400
            );
        }

        $location->delete();

        return response()->json(
            [
                "message" => "Location deleted successfully",
            ],
            204
        );
    }
}
