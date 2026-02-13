<?php

use App\Models\Ticket;
use App\Models\Sale;

it('lists tickets successfully', function () {
    $token = $this->getAuthToken();

    Ticket::factory()->count(3)->create();

    $response = $this->withToken($token)->getJson('/api/ticket');

    $response->assertOk()
             ->assertJsonCount(3)
             ->assertJsonStructure([
                 '*' => ['id', 'titre', 'description', 'created_at', 'updated_at'],
             ]);
});

it('fails to list tickets without auth', function () {
    Ticket::factory()->count(3)->create();

    $response = $this->getJson('/api/ticket');

    $response->assertUnauthorized();
});

it('creates a new ticket', function () {
    $token = $this->getAuthToken();

    $sale = Sale::factory()->create();

    // Crée un Sale valide
    $data = [
        'titre' => 'TicketTest',
        'description' => 'DescriptionTest',
        'sale_id' => $sale->id,
    ];

    $response = $this->withToken($token)->postJson('/api/ticket', $data);

    $response->assertCreated()
             ->assertJsonFragment([
                 'titre' => 'TicketTest',
                 'description' => 'DescriptionTest',
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('tickets', ['titre' => 'TicketTest']);
});

it('fails to create ticket with invalid data', function () {
    $token = $this->getAuthToken();

    $data = [
        'titre' => '', // Required field
        'sale_id' => 999, // Non-existent sale
    ];

    $response = $this->withToken($token)->postJson('/api/ticket', $data);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['titre', 'sale_id']);
});

it('shows a ticket successfully', function () {
    $token = $this->getAuthToken();

    $this->ticket = Ticket::factory()->create();

    $response = $this->withToken($token)->getJson("/api/ticket/{$this->ticket->id}");

    $response->assertOk()
             ->assertJsonFragment([
                 'id' => $this->ticket->id,
                 'titre' => $this->ticket->titre,
                 'description' => $this->ticket->description,
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'created_at', 'updated_at']);
});

it('fails to show nonexistent ticket', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->getJson('/api/ticket/999');

    $response->assertNotFound();
});

it('updates a ticket successfully', function () {
    $token = $this->getAuthToken();

    $sale = Sale::factory()->create();
    // Crée un Sale valide
    $this->ticket = Ticket::factory()->create();

    $data = [
        'titre' => 'UpdatedTicket',
        'description' => 'UpdatedDescription',
        'sale_id' => $sale->id, // Ajoute un sale_id valide
    ];

    $response = $this->withToken($token)->putJson("/api/ticket/{$this->ticket->id}", $data);

    $response->assertOk()
             ->assertJsonFragment([
                 'titre' => 'UpdatedTicket',
                 'description' => 'UpdatedDescription',
             ])
             ->assertJsonStructure(['id', 'titre', 'description', 'created_at', 'updated_at']);
});

it('deletes a ticket successfully', function () {
    $token = $this->getAuthToken();

    $this->ticket = Ticket::factory()->create();

    $response = $this->withToken($token)->deleteJson("/api/ticket/{$this->ticket->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tickets', ['id' => $this->ticket->id]);
});

it('fails to delete nonexistent ticket', function () {
    $token = $this->getAuthToken();

    $response = $this->withToken($token)->deleteJson('/api/ticket/999');

    $response->assertNotFound();
});

it('clears ticket cache when a ticket is created', function () {
    $token = $this->getAuthToken();
    Cache::put('tickets_list', ['old_ticket']);

    $sale = Sale::factory()->create();

    $response = $this->withToken($token)->postJson('/api/ticket', [
        'titre'       => 'Support Technique',
        'description' => 'Problème de connexion',
        'sale_id'     => $sale->id,
    ]);

    $response->assertCreated();
    expect(Cache::has('tickets_list'))->toBeFalse();
});

