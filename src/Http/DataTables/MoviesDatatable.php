<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\Movie;
use Yajra\DataTables\EloquentDataTable;

class MoviesDatatable extends DataTable
{
    protected bool $showTvSeries = false;

    protected string $urlPrefix = 'movies';

    public function query(Movie $model): Builder
    {
        return $model->newQuery()->withTranslation()
            ->with(['media'])
            ->where('is_tv_series', $this->showTvSeries);
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::actions(),
            Column::computed('thumbnail')
                ->title(__('movie::translation.thumbnail'))
                ->width(80),
            Column::editLink('name', admin_url($this->urlPrefix . '/{id}/edit'), __('movie::translation.name')),
            Column::make('origin_name')->title(__('movie::translation.origin_name')),
            Column::make('rating')->title(__('movie::translation.rating')),
            Column::make('year')->title(__('movie::translation.year')),
            Column::make('views')->title(__('movie::translation.views')),
            Column::createdAt(),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit("{$this->urlPrefix}/{$model->id}/edit")->can("{$this->urlPrefix}.edit"),
            Action::make(__('movie::translation.file_servers'), "{$this->urlPrefix}/{$model->id}/servers")
                ->icon('fas fa-server')
                ->can('servers.index'),
            Action::delete()->can('tv-series.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('tv-series.delete'),
            BulkAction::make(__('movie::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('movies.edit'),
        ];
    }

    public function renderColumns(EloquentDataTable $builder): EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('thumbnail', function (Movie $model) {
                $thumb = $model->getThumbnailUrl();

                return '<img src="' . $thumb . '" alt="" style="width: 100%; max-width: 80px;">';
            })
            ->editColumn('rating', function (Movie $model) {
                return $model->rating ? number_format($model->rating, 1) : '-';
            })
            ->rawColumns(['thumbnail']);
    }


    public function getActionUrl(): string
    {
        return admin_url($this->urlPrefix . '/bulk');
    }

    public function showTvSeries()
    {
        $this->showTvSeries = true;

        $this->urlPrefix = 'tv-series';

        return $this;
    }
}
