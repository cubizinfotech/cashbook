<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;

class PaymentmethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PaymentMethod::select('id', 'name', 'description', 'case_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Store a newly created resource.
     */
    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $paymentMethod = PaymentMethod::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'case_id'     => $validated['cash_id'], // mapped
            'status'      => 1,
            'created_by'  => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Payment method created successfully',
            'data'    => $paymentMethod,
        ], 201);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdatePaymentMethodRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $paymentMethod = PaymentMethod::findOrFail($id);

        $paymentMethod->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'case_id'     => $validated['case_id'],
            'updated_by'  => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Payment method updated successfully',
            'data'    => $paymentMethod,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        return response()->json([
            'message' => 'Payment method deleted successfully'
        ]);
    }
}
