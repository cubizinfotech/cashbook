<?php

use App\Models\Business;
use App\Models\Cashbook;
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

it('can list cashbooks', function () {
    actingAs($this->user, 'sanctum');

    Cashbook::factory()->count(3)->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    getJson('/api/cashbooks')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'business_id'],
            ],
        ]);
});

it('can create a cashbook', function () {
    actingAs($this->user, 'sanctum');

    $data = [
        'business_id' => $this->business->id,
        'title' => 'Test Cashbook',
        'description' => 'Test Description',
        'status' => 'active',
    ];

    postJson('/api/cashbooks', $data)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'title'],
        ])
        ->assertJson(['data' => ['title' => 'Test Cashbook']]);

    $this->assertDatabaseHas('cashbooks', [
        'title' => 'Test Cashbook',
        'business_id' => $this->business->id,
    ]);
});

it('can show a cashbook', function () {
    actingAs($this->user, 'sanctum');

    $cashbook = Cashbook::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    getJson("/api/cashbooks/{$cashbook->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'title', 'business_id'],
        ])
        ->assertJson(['data' => ['id' => $cashbook->id]]);
});

it('can update a cashbook', function () {
    actingAs($this->user, 'sanctum');

    $cashbook = Cashbook::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    $data = [
        'business_id' => $this->business->id,
        'title' => 'Updated Cashbook',
        'description' => 'Updated Description',
        'status' => 'active',
    ];

    putJson("/api/cashbooks/{$cashbook->id}", $data)
        ->assertStatus(200)
        ->assertJson(['data' => ['title' => 'Updated Cashbook']]);

    $this->assertDatabaseHas('cashbooks', [
        'id' => $cashbook->id,
        'title' => 'Updated Cashbook',
    ]);
});

it('can delete a cashbook', function () {
    actingAs($this->user, 'sanctum');

    $cashbook = Cashbook::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);

    deleteJson("/api/cashbooks/{$cashbook->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('cashbooks', ['id' => $cashbook->id]);
});

it('validates required fields when creating a cashbook', function () {
    actingAs($this->user, 'sanctum');

    postJson('/api/cashbooks', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['business_id', 'title']);
});

