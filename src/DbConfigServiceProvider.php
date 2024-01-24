<?php

namespace Postare\DbConfig;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Postare\DbConfig\Commands\DbConfigCommand;
use Postare\DbConfig\Testing\TestsDbConfig;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DbConfigServiceProvider extends PackageServiceProvider
{
    public static string $name = 'db-config';

    public static string $viewNamespace = 'db-config';

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
                    ->askToStarRepoOnGitHub('postare/db-config');
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
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/db-config/{$file->getFilename()}"),
                ], 'db-config-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsDbConfig());

        // Imposto la direttiva blade per ottenere le impostazioni
        Blade::directive('db_config', function ($expression) {
            return "<?php echo \Postare\DbConfig\DbConfig::get($expression); ?>";
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'postare/db-config';
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            DbConfigCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_db_config_table',
        ];
    }
}
