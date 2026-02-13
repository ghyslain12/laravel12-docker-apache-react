<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialStoreRequest;
use App\Http\Requests\MaterialUpdateRequest;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MaterialController extends Controller
{
    public function index(): JsonResponse
    {
        $materials = Cache::remember('materials_list', 3600, function () {
            return Material::all()->toArray();
        });

        return response()->json($materials);
    }

    public function store(MaterialStoreRequest $request): JsonResponse
    {
        $material = Material::create($request->validated());
        return response()->json($material, 201);
    }

    public function show(Material $material): JsonResponse
    {
        return response()->json($material);
    }

    public function update(MaterialUpdateRequest $request, Material $material): JsonResponse
    {
        $material->update($request->validated());
        return response()->json($material);
    }

    public function destroy(Material $material): JsonResponse
    {
        $material->delete();
        return response()->json(null, 204);
    }

    public function ping(): JsonResponse
    {
        return response()->json([
            'status'    => 'success',
            'message'   => 'pong',
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
