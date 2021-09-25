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
        $this->addAction(self::JUZAWEB_INIT_ACTION, [$this, 'addSettingForm']);
    }

    public function registerMovie()
    {
        HookAction::registerPostType('movies', [
            'label' => trans('mymo::app.movies'),
            'model' => Movie::class,
            'menu_position' => 11,
            'menu_icon' => 'fa fa-film',
            'supports' => ['tag'],
        ]);

        HookAction::addAdminMenu(
            trans('mymo::app.tv_series'),
            'tv-series',
            [
                'icon' => 'fa fa-film',
                'position' => 3,
                'parent' => 'movies',
            ]
        );

        HookAction::addAdminMenu(
            trans('mymo::app.sliders'),
            'sliders',
            [
                'icon' => 'fa fa-film',
                'position' => 6,
                'parent' => 'appearance',
            ]
        );
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
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('actors', 'movies', [
            'label' => trans('mymo::app.actors'),
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('directors', 'movies', [
            'label' => trans('mymo::app.directors'),
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('writers', 'movies', [
            'label' => trans('mymo::app.writers'),
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('years', 'movies', [
            'label' => trans('mymo::app.years'),
            'menu_position' => 8,
            'show_in_menu' => false,
            'supports' => [],
        ]);
    }

    public function addSettingForm()
    {
        HookAction::addSettingForm('mymo', [
            'name' => trans('mymo::app.mymo_setting'),
            'view' => view('mymo::setting.tmdb'),
            'priority' => 20
        ]);
    }
}
