<?php

namespace App\Http\Controllers\Api;

use App\Models\BusinessRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessRoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BusinessRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $businessRoles = BusinessRole::where('status', 'active')->orderBy('name')->get();

        return BusinessRoleResource::collection($businessRoles);
    }
}

