<?php

use Illuminate\Support\Facades\Route;
use Outerweb\NovaLinkPicker\Http\Controllers\Api\ModelOptionController;

Route::middleware(['nova:api'])
    ->prefix(config('nova-link-picker.api_base_url'))
    ->group(function () {
        Route::get('model-options/{modelClass}', [ModelOptionController::class, 'byModelClass'])
            ->where('modelClass', '.*')
            ->where('nova-link-picker', 'false')
            ->name('nova-vendor.nova-link-picker.model-options');
    });
