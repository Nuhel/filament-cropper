<?php

namespace Nuhel\FilamentCropper;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentCropperServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-cropper';

    protected array $styles = [
        'filament-cropper-style' => __DIR__ . '/../resources/dist/css/filament-cropper.css',
    ];

    protected array $scripts = [
        'filament-cropper-script' => __DIR__ . '/../resources/dist/js/filament-cropper.js',
    ];

    protected array $beforeCoreScripts = [
        'filament-croppie-cropper-component-script' => __DIR__ . '/../resources/dist/js/component.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasAssets()
            ->hasViews();
    }
}
