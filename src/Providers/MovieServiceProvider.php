<?php

namespace Juzaweb\Movie\Providers;

use Juzaweb\Movie\Actions\MenuAction;
use Juzaweb\Support\ServiceProvider;

class MovieServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAction([
            MenuAction::class
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}
