<?php

namespace App\Http\Controllers\Api;

use App\Models\Business;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BusinessResource;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $authId = auth()->id();

        $query = Business::where('created_by', $authId)
            ->orWhereHas('members', function ($q) use ($authId) {
                $q->where('user_id', $authId);
            });

        if ($request->has('status') && !empty($request->status)) {
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
        $user = auth()->user();

        $member = Member::where('email', $user->email)->first();

        // Debug: Log all request data
        Log::info('Business Store Request Data:', [
            'all' => $request->all(),
            'country_id' => $request->input('country_id'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
        ]);

        $data = $request->validated();

        // Debug: Log validated data
        Log::info('Business Store Validated Data:', $data);

        $data['created_by'] = auth()->id();
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
    public function show(Business $business)
    {
        $business->load(['country', 'state', 'city', 'members', 'cashbooks','users']);

        return response()->json([
            'data' => new BusinessResource($business),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        // Debug: Log all request data
        Log::info('Business Update Request Data:', [
            'all' => $request->all(),
            'country_id' => $request->input('country_id'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
        ]);

        $data = $request->validated();

        // Debug: Log validated data
        Log::info('Business Update Validated Data:', $data);

        $data['updated_by'] = auth()->id();

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

