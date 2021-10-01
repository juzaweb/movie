<?php

namespace Juzaweb\Movie\Actions;

use Illuminate\Support\Arr;
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
        $this->addAction(self::BACKEND_CALL_ACTION, [$this, 'addAdminMenus']);
        $this->addFilter(Action::FRONTEND_SEARCH_QUERY, [$this, 'applySearch'], 20, 2);
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
            'menu_box' => false,
            'menu_position' => 7,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('directors', 'movies', [
            'label' => trans('mymo::app.directors'),
            'menu_position' => 7,
            'menu_box' => false,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('writers', 'movies', [
            'label' => trans('mymo::app.writers'),
            'menu_position' => 7,
            'menu_box' => false,
            'supports' => [
                'thumbnail'
            ],
        ]);

        HookAction::registerTaxonomy('years', 'movies', [
            'label' => trans('mymo::app.years'),
            'menu_position' => 8,
            'show_in_menu' => false,
            'menu_box' => false,
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

    public function addAdminMenus()
    {
        HookAction::addAdminMenu(
            trans('mymo::app.video_ads'),
            'video-ads',
            [
                'icon' => 'fa fa-video-camera',
                'position' => 30,
                'parent' => 'setting',
            ]
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applySearch($builder, $params)
    {
        if ($formality = Arr::get($params, 'formality')) {
            $formality = $formality - 1;

            $builder->whereExists(function ($q) use ($formality) {
                $table = app(Movie::class)->getTable();

                $q->select(['id'])
                    ->from($table)
                    ->whereColumn("{$table}.id", '=', 'post_id')
                    ->where('tv_series', '=', $formality);
            });
        }

        return $builder;
    }
}
