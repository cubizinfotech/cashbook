<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = PaymentMethod::where('status', 'active')->orWhere('cashbook_id', null);

        if ($request->has('cashbook_id')) {
            $query = $query->orWhere('cashbook_id', $request->cashbook_id);
        }

        $paymentMethods = $query->orderBy('name')->get();

        return PaymentMethodResource::collection($paymentMethods);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['created_by']  = auth()->id();

        $paymentMethod = PaymentMethod::create($data);

        return response()->json([
            'message' => 'Payment method created successfully',
            'data' => new PaymentMethodResource($paymentMethod),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod): PaymentMethodResource
    {
        $paymentMethod->load(['cashbook', 'creator']);

        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $data = $request->validated();

        $data['updated_by'] = auth()->id();

        $paymentMethod->update($data);

        return response()->json([
            'message' => 'Payment method updated successfully',
            'data' => new PaymentMethodResource($paymentMethod),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        $paymentMethod->delete();

        return response()->json([
            'message' => 'Payment method deleted successfully'
        ]);
    }
}
