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
        $query = Cashbook::with(['business', 'entries', 'members']);

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
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

        $cashbooks = $query->latest()->paginate($request->get('per_page', 15));

        return CashbookResource::collection($cashbooks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCashbookRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $cashbook = Cashbook::create($data);
        $cashbook->load(['business', 'entries', 'members']);

        return response()->json([
            'message' => 'Cashbook created successfully',
            'data' => new CashbookResource($cashbook),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cashbook $cashbook): JsonResponse
    {
        $cashbook->load(['business', 'entries.category', 'entries.paymentMethod', 'members']);

        return response()->json([
            'data' => new CashbookResource($cashbook),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCashbookRequest $request, Cashbook $cashbook): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $cashbook->update($data);
        $cashbook->load(['business', 'entries', 'members']);

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

