<?php

namespace App\Http\Controllers\Api;

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
        $query = Member::with(['business', 'role', 'country', 'state', 'city']);

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
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate($request->get('per_page', 15));

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): JsonResponse
{
     $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('members/profile-pics', 'public');
        }

        // Create user account
        $password = $data['password'] ?? 'password';

        User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'created_by' => $data['created_by'],
            ]);

        // Create member
        unset($data['password']);
        $member = Member::create($data);
        $member->load(['business', 'role', 'country', 'state', 'city']);

        // Send registration email
        try {
            Mail::to($member->email)->send(new MemberRegistrationMail($member, $password));
        } catch (\Exception $e) {
            Log::error('Failed to send member registration email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Member created successfully',
            'data' => new MemberResource($member),
        ], 201);
}

    // public function store(Request  $request): JsonResponse
    // {
    //      echo'<pre>';print_r("test by gopal");'</pre>';exit;
    //     $data = $request->validated();
    //     $data['created_by'] = auth()->id();

    //     // Handle profile picture upload
    //     if ($request->hasFile('profile_pic')) {
    //         $data['profile_pic'] = $request->file('profile_pic')->store('members/profile-pics', 'public');
    //     }

    //     // Create user account
    //     $password = $data['password'] ?? 'password';
    //     // echo'<pre>';print_r($data);'</pre>';die;
    //     $user = User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($password),
    //         'email_verified_at' => now(),
    //         'created_by' => $data['created_by'],
    //     ]);

    //     // Create member
    //     unset($data['password']);
    //     $member = Member::create($data);
    //     $member->load(['business', 'role', 'country', 'state', 'city']);

    //     // Send registration email
    //     try {
    //         Mail::to($member->email)->send(new MemberRegistrationMail($member, $password));
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send member registration email: ' . $e->getMessage());
    //     }

    //     return response()->json([
    //         'message' => 'Member created successfully',
    //         'data' => new MemberResource($member),
    //     ], 201);
    // }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): JsonResponse
    {
        $member->load(['business', 'role', 'country', 'state', 'city']);

        return response()->json([
            'data' => new MemberResource($member),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete old profile picture
            if ($member->profile_pic) {
                Storage::disk('public')->delete($member->profile_pic);
            }
            $data['profile_pic'] = $request->file('profile_pic')->store('members/profile-pics', 'public');
        }

        // Update user password if provided
        // if (isset($data['password'])) {
        //     $user = User::where('email', $member->email)->first();
        //     if ($user) {
        //         $user->update(['password' => Hash::make($data['password'])]);
        //     }
        //     unset($data['password']);
        // }
        $user = User::where('email', $member->email)->first();

        if ($user) {

            // Update non-password fields ALWAYS
            $user->update($data);

            // Update password ONLY if provided
            if (!empty($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }
        }
        unset($data['password']);
        $member->update($data);
        $member->load(['business', 'role', 'country', 'state', 'city']);

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

