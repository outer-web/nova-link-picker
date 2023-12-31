<?php

namespace Outerweb\NovaLinkPicker\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Outerweb\NovaLinkPicker\Entities\Link;

class LinkPickerCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return config('nova-link-picker.link_entity', Link::class)::make($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
