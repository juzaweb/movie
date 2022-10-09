<?php

namespace Juzaweb\Movie\Providers;

use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\Movie\Commands\GenerateDemoMovieCommand;
use Juzaweb\Movie\Commands\GenerateDemoVideoCommand;
use Juzaweb\Movie\MovieAction;
use Juzaweb\CMS\Support\ServiceProvider;

class MovieServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        ActionRegister::register(MovieAction::class);

        $this->commands(
            [
                GenerateDemoMovieCommand::class,
                GenerateDemoVideoCommand::class
            ]
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
