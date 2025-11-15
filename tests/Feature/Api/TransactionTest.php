<?php

use App\Models\Business;
use App\Models\Cashbook;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->create(['created_by' => $this->user->id]);
    $this->cashbook = Cashbook::factory()->create([
        'business_id' => $this->business->id,
        'created_by' => $this->user->id,
    ]);
});

it('can list transactions', function () {
    actingAs($this->user, 'sanctum');

    Transaction::factory()->count(3)->create([
        'cashbook_id' => $this->cashbook->id,
        'created_by' => $this->user->id,
    ]);

    getJson('/api/transactions')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'cashbook_id', 'type', 'amount', 'party_name'],
            ],
        ]);
});

it('can create a transaction', function () {
    actingAs($this->user, 'sanctum');

    $data = [
        'cashbook_id' => $this->cashbook->id,
        'type' => 'in',
        'amount' => 1000.00,
        'party_name' => 'Test Party',
        'transaction_datetime' => now()->toDateTimeString(),
        'status' => 'active',
    ];

    postJson('/api/transactions', $data)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'type', 'amount', 'party_name'],
        ])
        ->assertJson(['data' => ['type' => 'in', 'party_name' => 'Test Party']]);

    $this->assertDatabaseHas('transactions', [
        'cashbook_id' => $this->cashbook->id,
        'type' => 'in',
        'party_name' => 'Test Party',
    ]);
});

it('can show a transaction', function () {
    actingAs($this->user, 'sanctum');

    $transaction = Transaction::factory()->create([
        'cashbook_id' => $this->cashbook->id,
        'created_by' => $this->user->id,
    ]);

    getJson("/api/transactions/{$transaction->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'cashbook_id', 'type', 'amount'],
        ])
        ->assertJson(['data' => ['id' => $transaction->id]]);
});

it('can update a transaction', function () {
    actingAs($this->user, 'sanctum');

    $transaction = Transaction::factory()->create([
        'cashbook_id' => $this->cashbook->id,
        'created_by' => $this->user->id,
    ]);

    $data = [
        'cashbook_id' => $this->cashbook->id,
        'type' => 'out',
        'amount' => 500.00,
        'party_name' => 'Updated Party',
        'transaction_datetime' => now()->toDateTimeString(),
        'status' => 'active',
    ];

    putJson("/api/transactions/{$transaction->id}", $data)
        ->assertStatus(200)
        ->assertJson(['data' => ['party_name' => 'Updated Party']]);

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'party_name' => 'Updated Party',
    ]);
});

it('can delete a transaction', function () {
    actingAs($this->user, 'sanctum');

    $transaction = Transaction::factory()->create([
        'cashbook_id' => $this->cashbook->id,
        'created_by' => $this->user->id,
    ]);

    deleteJson("/api/transactions/{$transaction->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
});

it('validates required fields when creating a transaction', function () {
    actingAs($this->user, 'sanctum');

    postJson('/api/transactions', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cashbook_id', 'type', 'amount', 'party_name', 'transaction_datetime']);
});

it('can filter transactions by type', function () {
    actingAs($this->user, 'sanctum');

    Transaction::factory()->create([
        'cashbook_id' => $this->cashbook->id,
        'type' => 'in',
        'created_by' => $this->user->id,
    ]);

    Transaction::factory()->create([
        'cashbook_id' => $this->cashbook->id,
        'type' => 'out',
        'created_by' => $this->user->id,
    ]);

    $response = getJson('/api/transactions?type=in')
        ->assertStatus(200);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['type'])->toBe('in');
});

