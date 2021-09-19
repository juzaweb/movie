<?php

namespace Juzaweb\Movie\Http\Datatable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Juzaweb\Abstracts\DataTable;
use Juzaweb\Movie\Models\Movie\Movie;

class MovieDatatable extends DataTable
{
    protected $tvSeries;

    public function mount($tvSeries = 0)
    {
        $this->tvSeries = $tvSeries;
    }

    /**
     * Columns datatable
     *
     * @return array
     */
    public function columns()
    {
        return [
            'thumbnail' => [
                'label' => trans('mymo::app.thumbnail'),
                'width' => '10%',
                'formatter' => function ($value, $row, $index) {
                    return '<img src="'. $row->getThumbnail() .'" class="w-100" />';
                }
            ],
            'title' => [
                'label' => trans('mymo::app.name'),
                'formatter' => [$this, 'rowActionsFormatter']
            ],
            'created_at' => [
                'label' => trans('juzaweb::app.created_at'),
                'width' => '15%',
                'align' => 'center',
                'formatter' => function ($value, $row, $index) {
                    return jw_date_format($row->created_at);
                }
            ]
        ];
    }

    /**
     * Query data datatable
     *
     * @param array $data
     * @return Builder
     */
    public function query($data)
    {
        $query = Movie::query();
        $query->where('tv_series', '=', $this->tvSeries);

        if ($keyword = Arr::get($data, 'keyword')) {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'like', '%'. $keyword .'%');
                $q->orWhere('description', 'like', '%'. $keyword .'%');
            });
        }

        return $query;
    }

    public function bulkActions($action, $ids)
    {
        switch ($action) {
            case 'delete':
                
                break;
        }
    }
}
