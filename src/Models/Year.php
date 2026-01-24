<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasSlug;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;

class Year extends Model
{
    use HasAPI, HasUuids,  UsedInFrontend, HasSlug, MenuBoxable;

    protected $table = 'movie_years';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_movie_year', 'movie_year_id', 'movie_id');
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder;
    }

    public function getEditUrl(): string
    {
        return route('admin.movie-years.edit', [$this->id]);
    }

    public function getUrl(): string
    {
        return home_url("year/{$this->slug}", $this->locale);
    }
}
