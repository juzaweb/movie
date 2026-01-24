<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasFrontendUrl;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;

class Genre extends Model implements TranslatableContract
{
    use HasAPI,
        HasUuids,
        Translatable,
        UsedInFrontend,
        HasFrontendUrl,
        MenuBoxable;

    protected $table = 'movie_genres';

    protected $translationForeignKey = 'movie_genre_id';

    protected $fillable = [
    ];

    public $translatedAttributes = [
        'name',
        'slug',
        'locale',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_movie_genre', 'movie_genre_id', 'movie_id');
    }

    public function scopeWhereInFrontend(Builder $builder, bool $cache = true): Builder
    {
        return $builder->withTranslation(null, null, true);
    }

    public function getEditUrl(): string
    {
        return route('admin.movie-genres.edit', [$this->id]);
    }

    public function getUrl(): string
    {
        return home_url("genre/{$this->slug}", $this->locale);
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }
}
