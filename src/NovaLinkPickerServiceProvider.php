<?php

namespace Outerweb\NovaLinkPicker;

use Laravel\Nova\Nova;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaLinkPickerServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        Nova::serving(function () {
            Nova::script('nova-link-picker', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-link-picker', __DIR__ . '/../dist/css/field.css');

            $locale = app()->getLocale();

            Nova::translations(__DIR__ . "/../resources/lang/{$locale}.json");
            Nova::translations(lang_path("vendor/nova-link-picker/$locale.json"));
        });
    }

    public function register()
    {
        parent::register();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('nova-link-picker')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoutes(['nova-vendor'])
            ->hasInstallCommand(function(InstallCommand $command) {
                $composerFile = file_get_contents(__DIR__ . '/../composer.json');

                if ($composerFile) {
                    $githubRepo = json_decode($composerFile, true)['homepage'] ?? null;

                    if ($githubRepo) {
                        $command
                            ->askToStarRepoOnGitHub($githubRepo);
                    }
                }
            });
    }
}
