<?php

namespace Outerweb\NovaLinkPicker\Entities;

use Exception;
use Illuminate\Support\Str;

class Link
{
    public $routeName;
    public $parameters;
    public $options;

    public function __construct(?string $routeName, array $parameters = [], array $options = [])
    {
        $this->routeName = $routeName;
        $this->parameters = collect($parameters);

        $this->options = collect($options)
            ->filter(function ($value, $key) {
                return $this->isOptionEnabled($key);
            });
    }

    public static function make(string|array|null $data): self
    {
        if (is_null($data)) {
            return new static(null);
        }

        if (! is_array($data)) {
            $data = json_decode($data, true);
        }

        return new static(
            $data['routeName'] ?? null,
            $data['parameters'] ?? [],
            $data['options'] ?? []
        );
    }

    public function route(): ?string
    {
        if ($this->routeName === '' || $this->routeName === null) {
            return null;
        }

        $parameters = $this->parameters
            ->mapWithKeys(function ($parameter) {
                return [$parameter['name'] => $parameter['value']];
            })
            ->toArray();

        if (Str::startsWith($this->routeName, 'external.')) {
            return match ($this->routeName) {
                'external.link' => $parameters['url'],
                'external.mailto' => "mailto:{$parameters['email']}",
                'external.tel' => "tel:{$parameters['phone']}",
                default => null,
            };
        }

        try {
            return route($this->routeName, $parameters);
        } catch (Exception $e) {
            return null;
        }
    }

    public function isDownload(): bool
    {
        if ($this->isOptionDisabled('download')) {
            return false;
        }

        return $this->options['download'] ?? false;
    }

    public function isExternal(): bool
    {
        return Str::startsWith($this->routeName, 'external.');
    }

    public function target(): ?string
    {
        if ($this->isOptionDisabled('open_in_new_tab')) {
            return '_self';
        }

        return ($this->options['open_in_new_tab'] ?? false) ? '_blank' : '_self';
    }

    public function renderAttributes(): string
    {
        $string = "href=\"{$this->route()}\" target=\"{$this->target()}\"";

        if ($this->isDownload()) {
            $string .= ' download';
        }

        return $string;
    }

    public function isOptionEnabled(string $option): bool
    {
        return collect(config('nova-link-picker.available_options', []))
            ->filter(fn ($value, $key) => $value === true)
            ->keys()
            ->contains($option);
    }

    public function isOptionDisabled(string $option): bool
    {
        return ! $this->isOptionEnabled($option);
    }

    public function toArray(): array
    {
        return [
            'routeName' => $this->routeName,
            'route' => $this->route(),
            'parameters' => $this->parameters,
            'options' => $this->options,
        ];
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
