<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Business::with(['country', 'state', 'city', 'members', 'cashbooks']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $businesses = $query->latest()->paginate($request->get('per_page', 15));

        return BusinessResource::collection($businesses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusinessRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        $business = Business::create($data);
        $business->load(['country', 'state', 'city', 'members', 'cashbooks']);

        return response()->json([
            'message' => 'Business created successfully',
            'data' => new BusinessResource($business),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Business $business): JsonResponse
    {
        $business->load(['country', 'state', 'city', 'members', 'cashbooks']);

        return response()->json([
            'data' => new BusinessResource($business),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($business->logo) {
                Storage::disk('public')->delete($business->logo);
            }
            $data['logo'] = $request->file('logo')->store('businesses/logos', 'public');
        }

        $business->update($data);
        $business->load(['country', 'state', 'city', 'members', 'cashbooks']);

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

