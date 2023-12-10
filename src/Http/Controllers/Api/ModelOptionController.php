<?php

namespace Outerweb\NovaLinkPicker\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Outerweb\NovaLinkPicker\Http\Controllers\Controller;
use Outerweb\NovaLinkPicker\Http\Resources\ModelOptionResource;

class ModelOptionController extends Controller
{
    public function byModelClass(string $modelClass): JsonResource
    {
        $modelClass = str_replace('/', '\\', $modelClass);

        if (! class_exists($modelClass)) {
            return response()->json([
                'message' => 'Model class not found.',
            ], 404);
        }

        $models = $modelClass::query()
            ->when(
                method_exists($modelClass, 'scopeVisibleForNovaLinkPicker'),
                fn ($query) => $query->visibleForNovaLinkPicker()
            )
            ->get();

        return ModelOptionResource::collection($models);
    }
}
