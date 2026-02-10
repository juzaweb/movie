<?php

namespace Juzaweb\Modules\Movie\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Juzaweb\Modules\Core\Contracts\Viewable;
use Juzaweb\Modules\Core\Enums\PostStatus;
use Juzaweb\Modules\Core\FileManager\Traits\HasMedia;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Models\Rating;
use Juzaweb\Modules\Core\Traits\HasAPI;
use Juzaweb\Modules\Core\Traits\HasContent;
use Juzaweb\Modules\Core\Traits\HasFrontendUrl;
use Juzaweb\Modules\Core\Traits\HasThumbnail;
use Juzaweb\Modules\Core\Traits\HasViews;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableAlias;

class Movie extends Model implements Viewable, TranslatableAlias
{
    use HasAPI,
        HasMedia,
        HasThumbnail,
        HasUuids,
        Translatable,
        UsedInFrontend,
        HasFrontendUrl,
        HasViews,
        HasContent;

    protected $table = 'movies';

    protected $fillable = [
        'origin_name',
        'rating',
        'tmdb_rating',
        'release',
        'year',
        'runtime',
        'video_quality',
        'trailer_link',
        'current_episode',
        'max_episode',
        'views',
        'is_tv_series',
        'is_paid',
        'status',
    ];

    public $translatedAttributes = [
        'name',
        'slug',
        'description',
        'content',
        'locale',
    ];

    public $mediaChannels = [
        'thumbnail',
        'poster',
    ];

    protected $casts = [
        'release' => 'date',
        'rating' => 'float',
        'views' => 'integer',
        'status' => PostStatus::class,
        'is_tv_series' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public $searchable = [
        'origin_name',
    ];

    public $translatedAttributeFormats = [
        'content' => 'html',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class, 'movie_id', 'id');
    }

    public function serverFiles()
    {
        return $this->hasManyThrough(ServerFile::class, Server::class, 'movie_id', 'server_id', 'id', 'id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_movie_genre', 'movie_id', 'movie_genre_id');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'movie_movie_country', 'movie_id', 'movie_country_id');
    }

    public function years(): BelongsToMany
    {
        return $this->belongsToMany(Year::class, 'movie_movie_year', 'movie_id', 'movie_year_id');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'movie_movie_actor', 'movie_id', 'movie_actor_id');
    }

    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'movie_movie_director', 'movie_id', 'movie_director_id');
    }

    public function writers(): BelongsToMany
    {
        return $this->belongsToMany(Writer::class, 'movie_movie_writer', 'movie_id', 'movie_writer_id');
    }

    public function scopeWhereInFrontend(Builder $builder, bool $cache): Builder
    {
        return $builder->withTranslation(null, null, $cache)
            ->with(
                [
                    'media' => fn($q) => $q->whereFrontend(),
                ]
            )
            ->where('status', PostStatus::PUBLISHED);
    }

    public function scopeAdditionSearch(Builder $builder, string $keyword): Builder
    {
        return $builder->orWhereHas(
            'translations',
            fn($q) => $q->where('name', 'like', '%' . $keyword . '%')
        );
    }

    public function getPosterAttribute()
    {
        return $this->getFirstMediaUrl('poster');
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnail;
    }

    public function getUrl(): string
    {
        if ($this->is_tv_series) {
            return home_url("tv-serie/{$this->slug}", $this->locale);
        }

        return home_url("movie/{$this->slug}", $this->locale);
    }

    public function syncActors(array $items = []): void
    {
        $ids = collect($items)->map(
            function ($item) {
                if (Str::isUuid($item)) {
                    return $item;
                }

                $actor = Actor::where('name', $item)->first();

                if (!$actor) {
                    $actor = Actor::create(['name' => $item]);
                }

                return $actor->id;
            }
        );

        $this->actors()->sync($ids);
    }

    public function syncDirectors(array $items = []): void
    {
        $ids = collect($items)->map(
            function ($item) {
                if (Str::isUuid($item)) {
                    return $item;
                }

                $director = Director::where('name', $item)->first();

                if (!$director) {
                    $director = Director::create(['name' => $item]);
                }

                return $director->id;
            }
        );

        $this->directors()->sync($ids);
    }

    public function syncWriters(array $items = []): void
    {
        $ids = collect($items)->map(
            function ($item) {
                if (Str::isUuid($item)) {
                    return $item;
                }

                $writer = Writer::where('name', $item)->first();

                if (!$writer) {
                    $writer = Writer::create(['name' => $item]);
                }

                return $writer->id;
            }
        );

        $this->writers()->sync($ids);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'ratingable');
    }
}
