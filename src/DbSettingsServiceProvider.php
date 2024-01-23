<?php

namespace Postare\DbSettings;

use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Postare\DbSettings\Commands\DbSettingsCommand;
use Postare\DbSettings\Testing\TestsDbSettings;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DbSettingsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'db-settings';

    public static string $viewNamespace = 'db-settings';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('postare/db-settings');
            });

        //        $configFileName = $package->shortName();
        //
        //        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
        //            $package->hasConfigFile();
        //        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        //        if (file_exists($package->basePath('/../resources/views'))) {
        //            $package->hasViews(static::$viewNamespace);
        //        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        //        // Asset Registration
        //        FilamentAsset::register(
        //            $this->getAssets(),
        //            $this->getAssetPackageName()
        //        );
        //
        //        FilamentAsset::registerScriptData(
        //            $this->getScriptData(),
        //            $this->getAssetPackageName()
        //        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/db-settings/{$file->getFilename()}"),
                ], 'db-settings-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsDbSettings());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'postare/db-settings';
    }

    //    /**
    //     * @return array<Asset>
    //     */
    //    protected function getAssets(): array
    //    {
    //        return [
    //            // AlpineComponent::make('db-settings', __DIR__ . '/../resources/dist/components/db-settings.js'),
    //            Css::make('db-settings-styles', __DIR__ . '/../resources/dist/db-settings.css'),
    //            Js::make('db-settings-scripts', __DIR__ . '/../resources/dist/db-settings.js'),
    //        ];
    //    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            DbSettingsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_db-settings_table',
        ];
    }
}