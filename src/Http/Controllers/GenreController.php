<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\GenresDataTable;
use Juzaweb\Modules\Movie\Http\Requests\GenreRequest;
use Juzaweb\Modules\Movie\Models\Genre;

class GenreController extends AdminController
{
    public function index(GenresDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_genres'));

        return $dataTable->render('movie::admin.genre.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_genres'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_genre'));

        $locale = $this->getFormLanguage();

        return view(
            'movie::admin.genre.form',
            [
                'model' => new Genre(),
                'action' => action([self::class, 'store']),
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Genre::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('movie::translation.movie_genres'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_genre_name', ['name' => $model->name]));

        return view(
            'movie::admin.genre.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'locale' => $locale,
            ]
        );
    }

    public function store(GenreRequest $request)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();

        $genre = DB::transaction(function () use ($data, $locale) {
            $model = new Genre();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.genre_created_successfully'),
                // 'redirect' => action([self::class, 'index']),
                'data' => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                ],
            ]
        );
    }

    public function update(GenreRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();
        $genre = Genre::findOrFail($id);
        $genre->setDefaultLocale($locale);

        DB::transaction(fn() => $genre->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.genre_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Genre::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.genre_updated_successfully'),
            ]
        );
    }
}
