<?php

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;


it('lists sales successfully', function () {
    $token = $this->getAuthToken();

    Sale::factory()->count(3)->create();

    $response = $this->withToken($token)->getJson('/api/sale');

    $response->assertOk()
             ->assertJsonCount(3)
             ->assertJsonStructure([
                 '*' => ['id', 'titre', 'description', 'customer_id', 'created_at', 'updated_at'],
             ]);
});

it('fails to list sales without auth', function () {
    Sale::factory()->count(3)->create();

    $response = $this->getJson('/api/sale');

    $response->assertUnauthorized();
});

it('creates a new sale', function () {
    $token = $this->getAuthToken();
    $user = User::factory()->create();

    $customer = Customer::factory()->create(['user_id' => $user->id]);

    $data = [
        'titre' => 'SaleTest',
        'description' => 'DescriptionTest',
        'customer_id' => $customer->id,
    ];

    $response = $this->withToken($token)->postJson('/api/sale', $data);

    $response->assertCreated()
             ->assertJsonFragment([
                 'titre' => 'SaleTest',
                 'description' => 'DescriptionTest',
                 'customer_id' => $customer->id,
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'customer_id', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('sales', ['titre' => 'SaleTest']);
});

it('fails to create sale with invalid data', function () {
    $token = $this->getAuthToken();

    $data = [
        'titre' => '',
        'customer_id' => 999,
    ];

    $response = $this->withToken($token)->postJson('/api/sale', $data);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['titre', 'customer_id']);
});

it('shows a sale successfully', function () {
    $token = $this->getAuthToken();

    $this->sale = Sale::factory()->create();

    $response = $this->withToken($token)->getJson("/api/sale/{$this->sale->id}");

    $response->assertOk()
             ->assertJsonFragment([
                 'id' => $this->sale->id,
                 'titre' => $this->sale->titre,
                 'description' => $this->sale->description,
                 'customer_id' => $this->sale->customer_id,
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'customer_id', 'created_at', 'updated_at']);
});

it('fails to show nonexistent sale', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->getJson('/api/sale/999');

    $response->assertNotFound();
});

it('updates a sale successfully', function () {
    $token = $this->getAuthToken();

    $this->sale = Sale::factory()->create();
    $customer = Customer::factory()->create();

    $data = [
        'titre' => 'UpdatedSale',
        'description' => 'UpdatedDescription',
        'customer_id' => $customer->id,
    ];

    $response = $this->withToken($token)->putJson("/api/sale/{$this->sale->id}", $data);

    $response->assertOk()
             ->assertJsonFragment([
                 'titre' => 'UpdatedSale',
                 'description' => 'UpdatedDescription',
                 'customer_id' => $customer->id,
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'customer_id', 'created_at', 'updated_at']);
});

it('deletes a sale successfully', function () {
    $token = $this->getAuthToken();

    $this->sale = Sale::factory()->create();

    $response = $this->withToken($token)->deleteJson("/api/sale/{$this->sale->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('sales', ['id' => $this->sale->id]);
});

it('fails to delete nonexistent sale', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->deleteJson('/api/sale/999');

    $response->assertNotFound();
});

it('clears sale cache when a sale is deleted', function () {
    $token = $this->getAuthToken();
    $sale = Sale::factory()->create();
    Cache::put('sales_list', [$sale]);

    $this->withToken($token)->deleteJson("/api/sale/{$sale->id}");

    expect(Cache::has('sales_list'))->toBeFalse();
});
