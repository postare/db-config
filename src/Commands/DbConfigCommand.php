<?php

namespace Postare\DbConfig\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class DbConfigCommand extends Command
{
    public $signature = 'make:settings {name} {panel?}';

    public $description = 'Create a new settings';

    /**
     * Filesystem instance
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        $this->createViewFromStub('filament.config-pages.' . str($this->argument('name'))->lower()->slug());

        if (! $this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->warn("File : {$path} already exits");
        }
    }

    /**
     * Create a new view file from the stub.
     *
     * @param  string  $viewName  The name of the view.
     */
    public function createViewFromStub(string $viewName): void
    {
        // Define the path to the view stub.
        $viewStubPath = __DIR__ . '/../../stubs/view.stub';

        // Define the path to the new view file.
        $newViewPath = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');

        if ($this->files->exists($newViewPath)) {
            $this->warn("File : {$newViewPath} already exists");
            return;
        }

        // Read the contents of the view stub.
        $viewStubContents = file_get_contents($viewStubPath);

        // Replace any variables in the stub contents.
        // In this example, we're replacing a variable named 'VIEW_NAME'.
        $viewContents = str_replace('$VIEW_NAME$', $viewName, $viewStubContents);

        // Create the directory for the new view file, if it doesn't already exist.
        $this->makeDirectory(dirname($newViewPath));

        // Write the view contents to the new view file.
        file_put_contents($newViewPath, $viewContents);

        $this->info("View file : {$newViewPath} created");
    }

    /**
     * Return the stub file path
     */
    public function getStubPath(): string
    {
        return __DIR__ . '/../../stubs/page.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     */
    public function getStubVariables(): array
    {
        return [
            'TITLE' => $this->getSingularClassName($this->argument('name')),
            'PANEL' => $this->argument('panel') ? ucfirst($this->argument('panel')) . '\\' : '',
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')),
            'SETTING_NAME' => str($this->argument('name'))->lower()->slug(),
        ];
    }

    /**
     * Get the stub path and the stub variables
     */
    public function getSourceFile(): string | array | bool
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     */
    public function getStubContents(string $stub, array $stubVariables = []): string | array | bool
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }

        return $contents;

    }

    /**
     * Get the full path of the generated class.
     */
    public function getSourceFilePath(): string
    {
        $panel = $this->argument('panel');
        $panelPrefix = $panel ? ucfirst($panel) . '\\' : '';

        $path = base_path('app\\Filament\\' . $panelPrefix . 'Pages') . '\\' . $this->getSingularClassName($this->argument('name')) . 'SettingsPage.php';
        return str_replace('\\', '/', $path);
    }

    /**
     * Return the Singular Capitalize Name
     */
    public function getSingularClassName($name): string
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     */
    protected function makeDirectory(string $path): string
    {

        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
