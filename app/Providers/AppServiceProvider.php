<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();
        $this->ensureStorageDirectories();
    }

    private function ensureStorageDirectories(): void
    {
        $dirs = [
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/cache/data'),
            storage_path('logs'),
            storage_path('app/public'),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }
    }
}
