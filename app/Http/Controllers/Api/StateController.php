<?php

namespace App\Http\Controllers\Api;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StateResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = State::where('status', 'active');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $states = $query->orderBy('name')->get();

        return StateResource::collection($states);
    }
}
