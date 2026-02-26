<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;
use Exception;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Replace the view finder to handle Windows path issues
        $this->app['view']->setFinder(
            new WindowsPathNormalizedFileViewFinder(
                $this->app['files'],
                $this->app['config']['view.paths'],
                $this->app['config']['view.extensions']
            )
        );

        // Register custom Blade directives for role checking
        Blade::if('hasrole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('hasanyrole', function ($roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        Blade::if('hasallroles', function ($roles) {
            return auth()->check() && auth()->user()->hasAllRoles($roles);
        });
    }
}

class WindowsPathNormalizedFileViewFinder extends FileViewFinder
{
    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param array $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                // Normalize path separators for Windows compatibility
                $viewPath = $path . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
                
                if ($this->files->exists($viewPath)) {
                    return $viewPath;
                }
            }
        }

        throw new \InvalidArgumentException("View [{$name}] not found.");
    }
}

