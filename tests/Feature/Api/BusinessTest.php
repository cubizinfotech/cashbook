<?php

use App\Models\Business;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list businesses', function () {
    actingAs($this->user, 'sanctum');

    Business::factory()->count(3)->create(['created_by' => $this->user->id]);

    getJson('/api/businesses')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'phone', 'status'],
            ],
        ]);
});

it('can create a business', function () {
    actingAs($this->user, 'sanctum');

    $data = [
        'name' => 'Test Business',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'status' => 'active',
    ];

    postJson('/api/businesses', $data)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email'],
        ])
        ->assertJson(['data' => ['name' => 'Test Business']]);

    $this->assertDatabaseHas('businesses', [
        'name' => 'Test Business',
        'email' => 'test@example.com',
    ]);
});

it('can show a business', function () {
    actingAs($this->user, 'sanctum');

    $business = Business::factory()->create(['created_by' => $this->user->id]);

    getJson("/api/businesses/{$business->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone'],
        ])
        ->assertJson(['data' => ['id' => $business->id]]);
});

it('can update a business', function () {
    actingAs($this->user, 'sanctum');

    $business = Business::factory()->create(['created_by' => $this->user->id]);

    $data = [
        'name' => 'Updated Business',
        'email' => 'updated@example.com',
        'phone' => '9876543210',
        'status' => 'active',
    ];

    putJson("/api/businesses/{$business->id}", $data)
        ->assertStatus(200)
        ->assertJson(['data' => ['name' => 'Updated Business']]);

    $this->assertDatabaseHas('businesses', [
        'id' => $business->id,
        'name' => 'Updated Business',
    ]);
});

it('can delete a business', function () {
    actingAs($this->user, 'sanctum');

    $business = Business::factory()->create(['created_by' => $this->user->id]);

    deleteJson("/api/businesses/{$business->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('businesses', ['id' => $business->id]);
});

it('validates required fields when creating a business', function () {
    actingAs($this->user, 'sanctum');

    postJson('/api/businesses', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

