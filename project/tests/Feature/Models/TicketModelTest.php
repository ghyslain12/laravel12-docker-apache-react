<?php

use App\Models\Ticket;
use App\Models\Sale;

it('creates a ticket with title and description', function () {
    $ticket = Ticket::factory()->create([
        'titre' => 'Fix Bug',
        'description' => 'Fixing API error'
    ]);

    expect($ticket->titre)->toBe('Fix Bug');
    $this->assertDatabaseHas('tickets', ['titre' => 'Fix Bug']);
});

it('has many sales', function () {
    $ticket = Ticket::factory()->create();
    $sales = Sale::factory()->count(2)->create();
    $ticket->sales()->attach($sales);

    expect($ticket->sales)->toHaveCount(2);
});
