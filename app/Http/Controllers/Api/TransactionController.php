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
        $query = Transaction::with(['cashbook', 'category', 'paymentMethod']);

        if ($request->has('cashbook_id')) {
            $query->where('cashbook_id', $request->cashbook_id);
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

        $transactions = $query->latest('transaction_datetime')->paginate($request->get('per_page', 15));

        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
          // Handle logo upload
        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('businesses/document', 'public');
        }

        $transaction = Transaction::create($data);
        $transaction->load(['cashbook', 'category', 'paymentMethod']);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['cashbook', 'category', 'paymentMethod']);

        return response()->json([
            'data' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // handle new document upload: store new file and delete old one if exists

          if ($request->hasFile('document')) {
            // Delete old logo
            if ($transaction->document) {
                Storage::disk('public')->delete($transaction->document);
            }
            $data['document'] = $request->file('document')->store('businesses/document', 'public');
        }

        $transaction->update($data);
        $transaction->load(['cashbook', 'category', 'paymentMethod']);

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
    // Delete file from storage if exists
    if (!empty($transaction->document)) {

        // CASE 1 — DB stores full path like: "documents/abc.jpg"
        if (Storage::disk('public')->exists($transaction->document)) {
            Storage::disk('public')->delete($transaction->document);
        }

        // CASE 2 — DB stores only filename like: "abc.jpg"
        if (Storage::disk('public')->exists('documents/' . $transaction->document)) {
            Storage::disk('public')->delete('documents/' . $transaction->document);
        }
    }

    // Delete DB record
    $transaction->delete();

    return response()->json([
        'message' => 'Transaction deleted successfully',
    ]);
}
}

