<?php

namespace App\Providers;

use App\Http\Controllers\Blog\FilesController;
use App\Http\Controllers\FileController;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(FileController::class)->needs(Filesystem::class)->give(function (Container $container) {
            return with($container->make(FilesystemManager::class), function (FilesystemManager $manager) {
                return $manager->disk('public');
            });
        });

        $this->app->when(FilesController::class)->needs(Filesystem::class)->give(function (Container $container) {
            return with($container->make(FilesystemManager::class), function (FilesystemManager $manager) {
                return $manager->disk('public');
            });
        });
    }
}
