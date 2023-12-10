<?php

namespace Outerweb\NovaLinkPicker\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Nova\Nova;

class ModelOptionResource extends JsonResource
{
    public function toArray($request)
    {
        $novaResource = Nova::newResourceFromModel($this->resource);

        $value = $this->getRouteKey();

        $label = $value;

        if ($novaResource) {
            $label = method_exists($novaResource, 'hrefTitle')
                ? $novaResource->hrefTitle()
                : $novaResource->title();
        }

        return [
            'value' => $value,
            'label' => $label,
        ];
    }
}
