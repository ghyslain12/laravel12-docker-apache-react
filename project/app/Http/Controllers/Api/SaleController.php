<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Requests\SaleUpdateRequest;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SaleController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = Cache::remember('sales_list', 3600, function () {
            return Sale::with(['customer', 'materials', 'tickets'])->get()->toArray();
        });
        return response()->json($sales);
    }

    public function store(SaleStoreRequest $request): JsonResponse
    {
        $sale = Sale::create($request->validated());

        if ($request->has('materials')) {
            $sale->materials()->attach($request->input('materials'));
        }

        return response()->json($sale->load(['customer', 'materials', 'tickets']), 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        return response()->json($sale->load(['customer', 'materials', 'tickets']));
    }

    public function update(SaleUpdateRequest $request, Sale $sale): JsonResponse
    {
        $sale->update($request->validated());

        if ($request->has('materials')) {
            $sale->materials()->sync($request->input('materials'));
        }

        return response()->json($sale->load(['customer', 'materials', 'tickets']));
    }

    public function destroy(Sale $sale): JsonResponse
    {
        $sale->tickets()->detach();
        $sale->delete();
        return response()->json(null, 204);
    }
}
