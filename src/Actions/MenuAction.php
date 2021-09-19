<?php

namespace Juzaweb\Movie\Actions;

use Juzaweb\Abstracts\Action;
use Juzaweb\Facades\HookAction;
use Juzaweb\Movie\Models\Movie\Movie;

class MenuAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle()
    {
        $this->addAction(self::JUZAWEB_INIT_ACTION, [$this, 'registerMovie']);
        $this->addAction(self::JUZAWEB_INIT_ACTION, [$this, 'registerTaxonomies']);
    }

    public function registerMovie()
    {
        HookAction::registerPostType('movies', [
            'label' => trans('mymo::app.movies'),
            'model' => Movie::class,
            'menu_icon' => 'fa fa-edit',
        ]);
    }

    public function registerTaxonomies()
    {
        HookAction::registerTaxonomy('genres', 'movies', [
            'label' => trans('mymo::app.genres'),
            'menu_position' => 6,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('countries', 'movies', [
            'label' => trans('mymo::app.countries'),
            'menu_icon' => 'fa fa-edit',
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);
    }
}
