<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessRoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BusinessRole::where('status', 'active');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->get();

        return response()->json(['data' => $roles]);
    }
}

