<?php

namespace App\Http\Controllers\Api;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BusinessResource;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 1);

        $query = Business::with(['country', 'state', 'city','members', 'cashbooks', 'creator']);

        $query->where('created_by', $userId)->orWhereHas('members', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('gst_number', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $businesses = $query->latest()->paginate($perPage);

        return BusinessResource::collection($businesses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        // Ensure location fields are properly set (convert empty strings to null)
        if (isset($data['country_id'])) {
            $data['country_id'] = !empty($data['country_id']) ? (int)$data['country_id'] : null;
        }
        if (isset($data['state_id'])) {
            $data['state_id'] = !empty($data['state_id']) ? (int)$data['state_id'] : null;
        }
        if (isset($data['city_id'])) {
            $data['city_id'] = !empty($data['city_id']) ? (int)$data['city_id'] : null;
        }

        $data['created_by'] = auth()->id();

        $business = Business::create($data);
        $business->load(['country', 'state', 'city', 'members', 'cashbooks', 'creator']);

        return response()->json([
            'message' => 'Business created successfully',
            'data' => new BusinessResource($business),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Business $business): BusinessResource
    {
        $business->load(['country', 'state', 'city', 'members', 'cashbooks', 'cashbooks.members', 'creator']);

        return new BusinessResource($business);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($business->logo) {
                Storage::disk('public')->delete($business->logo);
            }
            $data['logo'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        // Ensure location fields are properly set (convert empty strings to null)
        if (isset($data['country_id'])) {
            $data['country_id'] = !empty($data['country_id']) ? (int)$data['country_id'] : null;
        }
        if (isset($data['state_id'])) {
            $data['state_id'] = !empty($data['state_id']) ? (int)$data['state_id'] : null;
        }
        if (isset($data['city_id'])) {
            $data['city_id'] = !empty($data['city_id']) ? (int)$data['city_id'] : null;
        }

        $data['updated_by'] = auth()->id();

        $business->update($data);
        $business->load(['country', 'state', 'city', 'members', 'cashbooks', 'creator']);

        return response()->json([
            'message' => 'Business updated successfully',
            'data' => new BusinessResource($business),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Business $business): JsonResponse
    {
        $business->delete();

        return response()->json([
            'message' => 'Business deleted successfully',
        ]);
    }
}
