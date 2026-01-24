<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\ActorsDataTable;
use Juzaweb\Modules\Movie\Http\Requests\ActorRequest;
use Juzaweb\Modules\Movie\Models\Actor;

class ActorController extends AdminController
{
    public function index(ActorsDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_actors'));

        return $dataTable->render('movie::admin.actor.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_actors'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_actor'));

        $locale = $this->getFormLanguage();

        return view(
            'movie::admin.actor.form',
            [
                'model' => new Actor(),
                'action' => action([self::class, 'store']),
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Actor::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('movie::translation.movie_actors'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_actor_name', ['name' => $model->name]));

        return view(
            'movie::admin.actor.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'locale' => $locale,
            ]
        );
    }

    public function store(ActorRequest $request)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();

        $actor = DB::transaction(function () use ($data, $locale) {
            $model = new Actor();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.actor_created_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function update(ActorRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();
        $actor = Actor::findOrFail($id);
        $actor->setDefaultLocale($locale);

        DB::transaction(fn() => $actor->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.actor_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Actor::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.actor_updated_successfully'),
            ]
        );
    }
}
