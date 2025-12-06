<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\MemberRegistrationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 15);

        $query = Member::with(['user', 'business', 'businessRole', 'cashbooks', 'country', 'state', 'city', 'creator']);

        $query->where('created_by', $userId)->orWhereHas('business', function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhereHas('members', function ($q2) use ($userId) {
                    $q2->where('user_id', $userId)->orWhere('created_by', $userId);
                });
        });

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate($perPage);

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('members/profile-pics', 'public');
        }

        // Create user account
        $newUser = false;
        $password = $data['password'] ?? 'password';
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $newUser = true;
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make($password),
                'created_by' => auth()->id(),
            ]);
        }

        $exists = Member::where('user_id', $user->id)->where('business_id', $data['business_id'])->first();
        if ($exists) {
            return response()->json([
                'message' => 'This user is already added to this business.',
                'errors'  => [
                    'email' => ['This email already exists in this business.']
                ]
            ], 422);
        }

        if (auth()->id() == $user->id) {
            return response()->json([
                'message' => 'You cannot add yourself as a member to this business.',
                'errors'  => [
                    'email' => ['You cannot add yourself as a member to this business.']
                ]
            ], 422);
        }

        unset($data['password']);
        unset($data['email']);
        $data['user_id'] = $user->id;
        $data['created_by'] = auth()->id();

        $member = Member::create($data);
        $member->load(['user', 'business', 'businessRole', 'country', 'state', 'city', 'creator']);

        /*
        // Send registration email
        try {
            Mail::to($user->email)->send(
                new MemberRegistrationMail($member, $password, $newUser, $user)
            );
        } catch (Exception $e) {
            Log::error('Failed to send member registration email: ' . $e->getMessage());
        }
        */

        return response()->json([
            'message' => 'Member created successfully',
            'data' => new MemberResource($member),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): MemberResource
    {
        $member->load([ 'user', 'business', 'businessRole', 'cashbooks', 'country', 'state', 'city', 'creator']);

        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $data = $request->validated();

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete old profile picture
            if ($member->profile_pic) {
                Storage::disk('public')->delete($member->profile_pic);
            }
            $data['profile_pic'] = $request->file('profile_pic')->store('members/profile-pics', 'public');
        }

        // Create user account
        $newUser = false;
        $password = $data['password'] ?? 'password';
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $newUser = true;
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make($password),
                'created_by' => auth()->id(),
            ]);
        }

        if (auth()->id() == $user->id) {
            return response()->json([
                'message' => 'You cannot add yourself as a member to this business.',
                'errors'  => [
                    'email' => ['You cannot add yourself as a member to this business.']
                ]
            ], 422);
        }

        unset($data['password']);
        unset($data['email']);
        $data['user_id'] = $user->id;
        $data['updated_by'] = auth()->id();

        $member->update($data);
        $member->load(['user', 'business', 'businessRole', 'cashbooks', 'country', 'state', 'city', 'creator']);

        /*
        // Send registration email
        try {
            Mail::to($user->email)->send(
                new MemberRegistrationMail($member, $password, $newUser, $user)
            );
        } catch (Exception $e) {
            Log::error('Failed to send member registration email: ' . $e->getMessage());
        }
        */

        return response()->json([
            'message' => 'Member updated successfully',
            'data' => new MemberResource($member),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): JsonResponse
    {
        $member->delete();

        return response()->json([
            'message' => 'Member deleted successfully',
        ]);
    }
}

