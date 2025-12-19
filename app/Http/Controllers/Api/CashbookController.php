<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashbookRequest;
use App\Http\Requests\UpdateCashbookRequest;
use App\Http\Resources\CashbookResource;
use App\Models\Cashbook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CashbookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 15);

        $query = Cashbook::with(['business', 'members', 'members.user', 'entries', 'creator']);

        $query->where('created_by', $userId)->orWhereHas('business', function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhereHas('members', function ($q2) use ($userId) {
                    $q2->where('user_id', $userId)->orWhere('created_by', $userId);
                });
        });

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->has('member_id')) {
            $query->whereHas('members', function ($q) use ($request) {
                $q->where('id', $request->member_id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $cashbooks = $query->latest()->paginate($perPage);

        return CashbookResource::collection($cashbooks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCashbookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['created_by'] = auth()->id();
        $memberIds = $data['member_ids'] ?? [];
        unset($data['member_ids']);

        $cashbook = Cashbook::create($data);

        // Sync members if provided
        if (!empty($memberIds)) {
            $cashbook->members()->sync($memberIds);
        }

        $cashbook->load(['members']);

        return response()->json([
            'message' => 'Cashbook created successfully',
            'data' => new CashbookResource($cashbook),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cashbook $cashbook): CashbookResource
    {
        $cashbook->load(['business', 'members', 'members.user', 'entries', 'entries.category', 'entries.paymentMethod', 'creator']);

        return  new CashbookResource($cashbook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCashbookRequest $request, Cashbook $cashbook): JsonResponse
    {
        $data = $request->validated();

        $data['updated_by'] = auth()->id();
        $memberIds = $data['member_ids'] ?? null;
        unset($data['member_ids']);

        $cashbook->update($data);

        // Sync members if provided
        if ($memberIds != null) {
            $cashbook->members()->sync($memberIds);
        }

        $cashbook->load(['members']);

        return response()->json([
            'message' => 'Cashbook updated successfully',
            'data' => new CashbookResource($cashbook),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cashbook $cashbook): JsonResponse
    {
        $cashbook->delete();

        return response()->json([
            'message' => 'Cashbook deleted successfully',
        ]);
    }
}


