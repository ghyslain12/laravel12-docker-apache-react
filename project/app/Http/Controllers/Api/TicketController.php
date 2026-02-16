<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        $tickets = Cache::remember('tickets_list', 3600, function () {
            return Ticket::with('sales')->get()->toArray();
        });
        return response()->json($tickets);
    }

    public function store(TicketStoreRequest $request): JsonResponse
    {
        $ticket = Ticket::create($request->validated());

        if ($request->has('sale_id')) {
            $ticket->sales()->attach($request->sale_id);
        }

        return response()->json($ticket->load('sales'), 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json($ticket->load('sales'));
    }

    public function update(TicketUpdateRequest $request, Ticket $ticket): JsonResponse
    {
        $ticket->update($request->validated());

        if ($request->has('sale_id')) {
            $ticket->sales()->sync([$request->sale_id]);
            $ticket->touch();
        }

        return response()->json($ticket->load('sales'));
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();
        return response()->json(null, 204);
    }
}

