<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 1);

        $query = Transaction::with(['cashbook', 'category', 'paymentMethod', 'creator']);

        $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
            ->orWhereHas('cashbook', function ($qb) use ($userId) {
                $qb->where('created_by', $userId)
                    ->orWhereHas('business', function ($q1) use ($userId) {
                        $q1->where('created_by', $userId);
                    })
                    ->orWhereHas('members', function ($q2) use ($userId) {
                        $q2->where('user_id', $userId)
                            ->orWhere('created_by', $userId);
                    });
            });
        });

        if ($request->has('cashbook_id')) {
            $query->where('cashbook_id', $request->cashbook_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('party_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('transaction_datetime', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('transaction_datetime', '<=', $request->date_to);
        }

        $transactions = $query->latest('transaction_datetime')->paginate($perPage);

        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('businesses/document', 'public');
        }

        $data['created_by'] = auth()->id();

        $transaction = Transaction::create($data);
        $transaction->load(['category', 'paymentMethod']);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): TransactionResource
    {
        $transaction->load(['cashbook', 'cashbook.business', 'cashbook.members', 'cashbook.members.user', 'category', 'paymentMethod', 'creator']);

        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $data = $request->validated();

        // handle new document upload: store new file and delete old one if exists
        if ($request->hasFile('document')) {
            // Delete old logo
            if ($transaction->document) {
                Storage::disk('public')->delete($transaction->document);
            }
            $data['document'] = $request->file('document')->store('businesses/document', 'public');
        }

        $data['updated_by'] = auth()->id();

        $transaction->update($data);
        $transaction->load(['category', 'paymentMethod']);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'data' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully',
        ]);
    }
}

