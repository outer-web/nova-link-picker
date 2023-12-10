# Nova Link Picker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/outerweb/nova-link-picker.svg?style=flat-square)](https://packagist.org/packages/outerweb/nova-link-picker)
[![Total Downloads](https://img.shields.io/packagist/dt/outerweb/nova-link-picker.svg?style=flat-square)](https://packagist.org/packages/outerweb/nova-link-picker)

This package provides a Nova field to generate a link. It shows a select field with the routes of your application you want to be used as a link. It also provides fixed options like `external`, `mailto` and `tel`.

## Installation

You can install the package via composer:

```bash
composer require outerweb/nova-link-picker
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nova-link-picker-config"
```

This is the contents of the published config file:

```php
return [
    'api_base_url' => '/nova-vendor/outerweb/nova-link-picker',
    'available_options' => [
        'open_in_new_tab' => true,
        'download' => true,
    ]
];
```

## Usage

This field stores its value as a JSON string in the database. So you need to make sure the column in the database is a `json` column (or `text` if `json` is not (yet) supported by your DB engine).

The value of the field looks like this:

```json
{
    "routeName": string,
    "parameters": [
        [
            "name": string,
            "value": int|string
        ]
    ],
    "options": {
        "openInNewTab": bool,
        "download": bool
    }
}
```

To make it easier to work with this value, you can use the `Outerweb\NovaLinkPicker\Casts'LinkPickerCast` cast. 

```php

use Outerweb\NovaLinkPicker\Casts\LinkPickerCast;

...

protected $casts = [
    'link' => LinkPickerCast::class,
];

```

This cast will cast the value to a `Outerweb\NovaLinkPicker\Entities'Link` instance.

On this instance you can call the following methods / properties:

```php

// Returns the route name as a string
// The routeName value can come from your applications registered routes
// or from the fixed options when it starts with `external.`:
// - `external.url` for an external url
// - `external.mailto` for a mailto link
// - `external.tel` for a tel link
$link->routeName;

// Returns the parameters as an array
$link->parameters;

// Returns the options as an array
$link->options;

// Generate the route (just like you would do with Laravel's `route()` helper)
$link->route();

// Get the target attribute value for the link
$link->target();

// Check if the link is an external link
$link->isExternal();

// Check if the link should be a download link
$link->isDownload();

// Render the link attributes (`href`, `target` and optionally `download`)
// Example: <a {{ $link->renderAttributes() }}>Click me!</a>
$link->renderAttributes();

```

Add the field to your Nova resource:

```php
use Outerweb\NovaLinkPicker\Nova\Fields\LinkPicker;

...

public function fields(Request $request)
{
    return [
        LinkPicker::make('Link')
            // This will Str::limit() the value on the Nova index table
            ->abbreviated()
            // This will hide the `download` option
            ->withoutDownload()
            // This will hide the `open in new tab` option
            ->withoutOpenInNewTab(),
    ];
}
```

## Overwriting

Sometimes your application requires some custom logic. We've tried to make it as easy as possible to overwrite the default behavior.

You can overwrite the `LinkPicker` field by creating a new field that extends the `LinkPicker` field. This way, you can just overwrite the methods you want to change.

```php

namespace App\Nova\Fields;

use Outerweb\NovaLinkPicker\Nova\Fields\LinkPicker as BaseLinkPicker;

class LinkPicker extends BaseLinkPicker
{
    // Your custom code here
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Simon Broekaert](https://github.com/SimonBroekaert)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
