<?php

namespace Outerweb\NovaLinkPicker\Nova\Fields;

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Outerweb\NovaLinkPicker\Entities\Link;

class LinkPicker extends Field
{
    public $withoutDownloadOption = false;
    public $withoutOpenInNewTabOption = false;

    public $component = 'nova-link-picker';

    public function resolve($resource, $attribute = null)
    {
        $this->withMeta(array_merge(
            [
                'abbreviated' => false,
                'apiBaseUrl' => config('nova-link-picker.api_base_url'),
                'availableRoutes' => $this->fetchAvailableRoutes(),
                'availableOptions' => $this->fetchAvailableOptions(),
            ],
            $this->meta
        ));

        parent::resolve($resource, $attribute);
    }

    public function resolveForDisplay($resource, $attribute = null)
    {
        parent::resolveForDisplay($resource, $attribute);

        if (! $this->value instanceof Link) {
            $this->value = Link::make($this->value);
        }

        $this->withMeta([
            'href' => $this->value->route(),
            'target' => $this->value->target(),
            'isDownload' => $this->value->isDownload(),
        ]);
    }

    public function abbreviated()
    {
        return $this->withMeta(['abbreviated' => true]);
    }

    public function withoutDownloadOption()
    {
        $this->withoutDownloadOption = true;

        return $this;
    }

    public function withoutOpenInNewTabOption()
    {
        $this->withoutOpenInNewTabOption = true;

        return $this;
    }

    public function getRouteNameForRoute(RoutingRoute $route)
    {
        return $route->getName();
    }

    public function fetchAvailableRoutes()
    {
        return collect(Route::getRoutes()->getRoutesByName())
            ->filter(function (RoutingRoute $route) {
                return $this->isGetRoute($route) && $this->isVisibleRoute($route);
            })
            ->map(function (RoutingRoute $route) {
                $routeName = $this->getRouteNameForRoute($route);

                $signatureParameters = collect($route->signatureParameters());

                $label = $route->wheres['nova-link-picker-label'] ?? Str::of($routeName)
                    ->explode('.')
                    ->map(fn (string $part) => Str::title($part))
                    ->implode(' > ');

                return [
                    'label' => $label,
                    'name' => $routeName,
                    'uri' => $route->uri(),
                    'parameters' => collect($route->parameterNames())
                        ->map(function (string $parameter) use ($signatureParameters) {
                            $model = null;
                            $type = 'string';
                            $isOptional = true;

                            if ($signatureParameters->contains('name', $parameter)) {
                                /** @var ReflectionParameter $reflectionParameter */
                                $reflectionParameter = $signatureParameters->firstWhere('name', $parameter);

                                $model = $reflectionParameter?->getType()?->getName();
                                $type = $model ? 'model' : 'string';
                                $isOptional = $reflectionParameter?->allowsNull() ?? true;
                            }

                            return [
                                'name' => $parameter,
                                'label' => Str::title($parameter),
                                'type' => $type,
                                'model' => $model,
                                'isOptional' => $isOptional,
                            ];
                        }),
                ];
            })
            ->unique('name')
            ->sortBy('name')
            ->toArray();
    }

    public function fetchAvailableOptions()
    {
        $availableOptions = collect(config('nova-link-picker.available_options'));

        $options = [];

        if ($availableOptions->get('open_in_new_tab') === true && ! $this->withoutOpenInNewTabOption) {
            $options[] = [
                'type' => 'checkbox',
                'label' => __('Open in new tab'),
                'name' => 'open_in_new_tab',
                'value' => false,
            ];
        }

        if ($availableOptions->get('download') === true && ! $this->withoutDownloadOption) {
            $options[] = [
                'type' => 'checkbox',
                'label' => __('Download'),
                'name' => 'download',
                'value' => false,
            ];
        }

        return $options;
    }

    public function isGetRoute(RoutingRoute $route)
    {
        return collect($route->methods())->contains('GET');
    }

    public function isVisibleRoute(RoutingRoute $route)
    {
        if (isset($route->wheres['nova-link-picker'])) {
            return $route->wheres['nova-link-picker'] === 'true';
        }

        return false;
    }
}
