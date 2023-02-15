<?php

namespace Juzaweb\Movie;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\Movie\Http\Controllers\AjaxController;
use Juzaweb\Movie\Http\Controllers\Backend\TmdbController;

class MovieAction extends Action
{
    public function handle()
    {
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'registerMovie']
        );
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'registerTaxonomies']
        );
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'addSettingForm']
        );
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'registerResources']
        );
        $this->addAction(
            Action::FRONTEND_CALL_ACTION,
            [$this, 'addAjaxTheme']
        );
        $this->addAction(
            'post_type.movies.btn_group',
            [$this, 'addImportButton']
        );
        $this->addAction(
            'post_type.movies.index',
            [$this, 'addModalImport']
        );
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'addAjaxAdmin']
        );
        $this->addAction(
            Action::INIT_ACTION,
            [$this, 'addVideoAdsType']
        );
    }

    public function registerMovie()
    {
        HookAction::registerPostType(
            'movies',
            [
                'label' => trans('mymo::app.movies'),
                'menu_position' => 11,
                'menu_icon' => 'fa fa-film',
                'supports' => ['tag'],
                'metas' => [
                    'origin_title' => [
                        'label' => trans('mymo::app.other_name')
                    ],
                    'tv_series' => [
                        'label' => trans('mymo::app.type'),
                        'type' => 'select',
                        'sidebar' => true,
                        'data' => [
                            'options' => [
                                '0' => trans('mymo::app.movie'),
                                '1' => trans('mymo::app.tv_series'),
                            ]
                        ],
                    ],
                    'poster' => [
                        'label' => trans('mymo::app.poster'),
                        'type' => 'image',
                        'sidebar' => true,
                    ],
                    'rating' => [
                        'label' => trans('mymo::app.rating')
                    ],
                    'release' => [
                        'label' => trans('mymo::app.release'),
                        'data' => [
                            'class' => 'datepicker',
                        ]
                    ],
                    'year' => [
                        'label' => trans('mymo::app.year')
                    ],
                    'runtime' => [
                        'label' => trans('mymo::app.runtime')
                    ],
                    'video_quality' => [
                        'label' => trans('mymo::app.video_quality')
                    ],
                    'trailer_link' => [
                        'label' => trans('mymo::app.trailer')
                    ],
                    'current_episode' => [
                        'label' => trans('mymo::app.current_episode')
                    ],
                    'max_episode' => [
                        'label' => trans('mymo::app.max_episode')
                    ]
                ],
            ]
        );

        /*HookAction::registerPostType(
            'lives',
            [
                'label' => 'Live TVs',
                'menu_position' => 12,
                'menu_icon' => 'fa fa-film',
                'supports' => ['tag'],
            ]
        );*/
    }

    public function registerTaxonomies()
    {
        HookAction::registerTaxonomy(
            'genres',
            'movies',
            [
                'label' => trans('mymo::app.genres'),
                'menu_position' => 6,
                'supports' => [
                    'thumbnail'
                ],
            ]
        );

        HookAction::registerTaxonomy(
            'countries',
            'movies',
            [
                'label' => trans('mymo::app.countries'),
                'menu_position' => 7,
                'supports' => [
                    'thumbnail'
                ],
            ]
        );

        HookAction::registerTaxonomy(
            'actors',
            'movies',
            [
                'label' => trans('mymo::app.actors'),
                'menu_box' => false,
                'menu_position' => 7,
                'supports' => [
                    'thumbnail'
                ],
            ]
        );

        HookAction::registerTaxonomy(
            'directors',
            'movies',
            [
                'label' => trans('mymo::app.directors'),
                'menu_position' => 7,
                'menu_box' => false,
                'supports' => [
                    'thumbnail'
                ],
            ]
        );

        HookAction::registerTaxonomy(
            'writers',
            'movies',
            [
                'label' => trans('mymo::app.writers'),
                'menu_position' => 7,
                'menu_box' => false,
                'supports' => [
                    'thumbnail'
                ],
            ]
        );

        HookAction::registerTaxonomy(
            'years',
            'movies',
            [
                'label' => trans('mymo::app.years'),
                'menu_position' => 8,
                'show_in_menu' => false,
                'supports' => [],
            ]
        );
    }

    public function addSettingForm()
    {
        HookAction::registerConfig(
            [
                "tmdb_api_key",
                "player_watermark",
                "player_watermark_logo",
                "mymo_movie_report",
            ]
        );

        HookAction::addSettingForm(
            'mymo',
            [
                'name' => trans('mymo::app.mymo_setting'),
                'view' => view('mymo::setting.tmdb'),
                'priority' => 20,
            ]
        );
    }

    public function addAjaxTheme()
    {
        HookAction::registerFrontendAjax(
            'movie-download',
            [
                'callback' => [AjaxController::class, 'download']
            ]
        );

        HookAction::registerFrontendAjax(
            'get-player',
            [
                'callback' => [AjaxController::class, 'getPlayer']
            ]
        );

        HookAction::registerFrontendAjax(
            'popular-movies',
            [
                'callback' => [AjaxController::class, 'getPopularMovies']
            ]
        );

        HookAction::registerFrontendAjax(
            'movies-genre',
            [
                'callback' => [AjaxController::class, 'getMoviesByGenre']
            ]
        );

        HookAction::registerFrontendAjax(
            'mymo-filter-form',
            [
                'callback' => [AjaxController::class, 'getFilterForm'],
            ]
        );

        HookAction::registerFrontendAjax(
            'movie-report',
            [
                'callback' => [AjaxController::class, 'report'],
                'method' => 'post',
            ]
        );
    }

    public function registerResources()
    {
        HookAction::registerResource(
            'servers',
            'movies',
            [
                'label' => trans('mymo::app.servers'),
                'label_action' => trans('mymo::app.upload'),
                'menu' => [
                    'icon' => 'fa fa-server',
                ],
            ]
        );

        HookAction::registerResource(
            'download',
            'movies',
            [
                'label' => trans('mymo::app.download'),
                'label_action' => trans('mymo::app.download'),
                'menu' => [
                    'icon' => 'fa fa-download',
                ],
                'metas' => [
                    'url' => [
                        'label' => trans('mymo::app.url'),
                    ],
                ]
            ]
        );

        HookAction::registerResource(
            'files',
            'movies',
            [
                'label' => trans('mymo::app.upload_videos'),
                'label_action' => trans('mymo::app.upload_videos'),
                'parent' => 'servers',
                'metas' => [
                    'source' => [
                        'label' => trans('mymo::app.source'),
                        'type' => 'select',
                        'data' => [
                            'options' => [
                                'mp4' => 'MP4 From URL',
                                'youtube' => 'Youtube',
                                'vimeo' => 'Vimeo',
                                'gdrive' => 'Google Drive',
                                'mkv' => 'MKV From URL',
                                'webm' => 'WEBM From URL',
                                'm3u8' => 'M3U8 From URL',
                                'embed' => 'Embed URL',
                            ]
                        ]
                    ],
                    'url' => [
                        'label' => trans('mymo::app.url'),
                        'type' => 'upload_url'
                    ],
                ],
            ]
        );

        HookAction::registerResource(
            'subtitles',
            'movies',
            [
                'label' => trans('mymo::app.subtitles'),
                'label_action' => trans('mymo::app.subtitles'),
                'parent' => 'files',
                'metas' => [
                    'url' => [
                        'label' => trans('mymo::app.url'),
                    ],
                ],
            ]
        );

        HookAction::registerResource(
            'movie-reports',
            null,
            [
                'label' => trans('mymo::app.movie_reports'),
                'has_display_order' => false,
                'menu' => [
                    'icon' => 'fa fa-bug',
                    'parent' => 'setting',
                    'priority' => 99,
                ],
                'metas' => [
                    'type' => [
                        'type' => 'select',
                        'data' => [
                            'disabled' => true,
                            'options' => [
                                'die_link' => 'Link is dead',
                                'bug' => 'Bug',
                                'other' => 'Other',
                            ],
                        ],
                    ]
                ]
            ]
        );
    }

    public function addImportButton()
    {
        echo '<a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#tmdb-modal">
        <i class="fa fa-download"></i> '. trans('mymo::app.add_from_tmdb') .'
        </a>';
    }

    public function addModalImport()
    {
        echo view('mymo::tmdb_import')
            ->render();
    }

    public function addAjaxAdmin()
    {
        HookAction::registerAdminAjax(
            'tmdb-add_movie',
            [
                'callback' => [TmdbController::class, 'addMovie'],
                'method' => 'post'
            ]
        );
    }

    public function addVideoAdsType()
    {
        if (\Juzaweb\CMS\Support\HookAction::hasMacro('registerAdsPosition')) {
            $this->hookAction->registerAdsPosition(
                'movie',
                'video',
                ['name' => 'Movie Video Ads']
            );
        }
    }
}
