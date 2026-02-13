<?php

use App\Models\Material;


it('lists materials successfully', function () {
    $token = $this->getAuthToken();

    Material::factory()->count(3)->create();

    $response = $this->withToken($token)->getJson('/api/material');
    $response->assertOk()
             ->assertJsonCount(3)
             ->assertJsonStructure([
                 '*' => ['id', 'designation', 'created_at', 'updated_at'],
             ]);
});

it('fails to list materials without auth', function () {
    Material::factory()->count(3)->create();

    $response = $this->getJson('/api/material');

    $response->assertUnauthorized();
});

it('creates a new material', function () {
    $token = $this->getAuthToken();

    $data = [
        'designation' => 'MaterialTest',
    ];

    $response = $this->withToken($token)->postJson('/api/material', $data);

    $response->assertCreated()
             ->assertJsonFragment([
                 'designation' => 'MaterialTest',
             ])
             ->assertJsonStructure(['id', 'designation', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('materials', ['designation' => 'MaterialTest']);
});

it('fails to create material with invalid data', function () {
    $token = $this->getAuthToken();

    $data = [
        'designation' => '', // Required field
    ];

    $response = $this->withToken($token)->postJson('/api/material', $data);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors('designation');
});

it('shows a material successfully', function () {
    $token = $this->getAuthToken();

    $this->material = Material::factory()->create();

    $response = $this->withToken($token)->getJson("/api/material/{$this->material->id}");

    $response->assertOk()
             ->assertJsonFragment([
                 'id' => $this->material->id,
                 'designation' => $this->material->designation,
             ])
             ->assertJsonStructure(['id', 'designation', 'created_at', 'updated_at']);
});

it('fails to show nonexistent material', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->getJson('/api/material/999');

    $response->assertNotFound();
});

it('updates a material successfully', function () {
    $token = $this->getAuthToken();

    $this->material = Material::factory()->create();

    $data = [
        'designation' => 'UpdatedMaterial',
    ];

    $response = $this->withToken($token)->putJson("/api/material/{$this->material->id}", $data);

    $response->assertOk()
             ->assertJsonFragment([
                 'designation' => 'UpdatedMaterial',
             ])
             ->assertJsonStructure(['id', 'designation', 'created_at', 'updated_at']);
});

it('deletes a material successfully', function () {
    $token = $this->getAuthToken();

    $this->material = Material::factory()->create();

    $response = $this->withToken($token)->deleteJson("/api/material/{$this->material->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('materials', ['id' => $this->material->id]);
});

it('fails to delete nonexistent material', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->deleteJson('/api/material/999');

    $response->assertNotFound();
});

it('clears material cache when a material is updated', function () {
    $token = $this->getAuthToken();
    $material = Material::factory()->create();
    Cache::put('materials_list', [$material]);

    $this->withToken($token)->putJson("/api/material/{$material->id}", [
        'designation' => 'Updated Material'
    ]);

    expect(Cache::has('materials_list'))->toBeFalse();
});
