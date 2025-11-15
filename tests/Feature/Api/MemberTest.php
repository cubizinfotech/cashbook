<?php

use App\Models\Business;
use App\Models\Member;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->create(['created_by' => $this->user->id]);
});

it('can list members', function () {
    actingAs($this->user, 'sanctum');

    Member::factory()->count(3)->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    getJson('/api/members')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'business_id'],
            ],
        ]);
});

it('can create a member', function () {
    actingAs($this->user, 'sanctum');

    $data = [
        'business_id' => $this->business->id,
        'name' => 'Test Member',
        'email' => 'member@example.com',
        'phone' => '1234567890',
        'status' => 'active',
    ];

    postJson('/api/members', $data)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email'],
        ])
        ->assertJson(['data' => ['name' => 'Test Member']]);

    $this->assertDatabaseHas('members', [
        'name' => 'Test Member',
        'email' => 'member@example.com',
    ]);
});

it('can show a member', function () {
    actingAs($this->user, 'sanctum');

    $member = Member::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    getJson("/api/members/{$member->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'business_id'],
        ])
        ->assertJson(['data' => ['id' => $member->id]]);
});

it('can update a member', function () {
    actingAs($this->user, 'sanctum');

    $member = Member::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    $data = [
        'business_id' => $this->business->id,
        'name' => 'Updated Member',
        'email' => 'updated@example.com',
        'phone' => '9876543210',
        'status' => 'active',
    ];

    putJson("/api/members/{$member->id}", $data)
        ->assertStatus(200)
        ->assertJson(['data' => ['name' => 'Updated Member']]);

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'name' => 'Updated Member',
    ]);
});

it('can delete a member', function () {
    actingAs($this->user, 'sanctum');

    $member = Member::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    deleteJson("/api/members/{$member->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('members', ['id' => $member->id]);
});

it('validates required fields when creating a member', function () {
    actingAs($this->user, 'sanctum');

    postJson('/api/members', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['business_id', 'name', 'email']);
});

