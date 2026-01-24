<?php

namespace Juzaweb\Modules\Movie\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\Movie\Models\LiveTv;

class LiveTvsDatatable extends DataTable
{
    protected string $actionUrl = 'live-tvs/bulk';

    public function query(LiveTv $model): Builder
    {
        return $model->newQuery()->withTranslation()->with('media');
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
            Column::make('name')->title(__('movie::translation.name')),
            Column::make('streaming_url')->title(__('movie::translation.streaming_url')),
            Column::make('views')->title(__('movie::translation.views')),
            Column::createdAt(),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit("live-tvs/{$model->id}/edit")->can('live-tvs.edit'),
            Action::delete()->can('live-tvs.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('live-tvs.delete'),
        ];
    }

    public function renderColumns(\Yajra\DataTables\EloquentDataTable $builder): \Yajra\DataTables\EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('thumbnail', function (LiveTv $model) {
                $thumb = $model->getThumbnailUrl();
                return '<img src="'.$thumb.'" alt="" style="width: 100%; max-width: 80px;">';
            })
            ->rawColumns(['thumbnail']);
    }
}
