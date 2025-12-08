<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['created_by']  = auth()->id();

        $paymentMethod = Category::create($data);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($paymentMethod),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $paymentMethod): CategoryResource
    {
        $paymentMethod->load(['cashbook', 'creator']);

        return new CategoryResource($paymentMethod);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateCategoryRequest $request, Category $paymentMethod): JsonResponse
    {
        $data = $request->validated();

        $data['updated_by'] = auth()->id();

        $paymentMethod->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($paymentMethod),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $paymentMethod): JsonResponse
    {
        $paymentMethod->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
