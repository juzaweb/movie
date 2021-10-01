<?php

namespace Juzaweb\Movie\Models\Movie;

use Illuminate\Support\Arr;
use Juzaweb\Models\Model;
use Juzaweb\Models\Taxonomy;
use Juzaweb\Movie\Models\Video\VideoServer;
use Juzaweb\Traits\PostTypeModel;
use Juzaweb\Movie\Models\DownloadLink;
use Illuminate\Database\Eloquent\Builder;

/**
 * Juzaweb\Movie\Models\Movie\Movie
 *
 * @property int $id
 * @property string $title
 * @property string|null $origin_title
 * @property string|null $thumbnail
 * @property string|null $poster
 * @property string $slug
 * @property string|null $description
 * @property string|null $content
 * @property string|null $rating
 * @property string|null $release
 * @property int|null $year
 * @property string|null $runtime
 * @property string|null $video_quality
 * @property string|null $trailer_link
 * @property int|null $current_episode
 * @property int|null $max_episode
 * @property int $tv_series
 * @property int $is_paid
 * @property string $status
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Juzaweb\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|DownloadLink[] $downloadLinks
 * @property-read int|null $download_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Movie\Models\Movie\MovieRating[] $movieRating
 * @property-read int|null $movie_rating_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Movie\Models\Video\VideoServer[] $servers
 * @property-read int|null $servers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Juzaweb\Models\Taxonomy[] $taxonomies
 * @property-read int|null $taxonomies_count
 * @property-read \Juzaweb\Models\User $updatedBy
 * @method static Builder|Movie newModelQuery()
 * @method static Builder|Movie newQuery()
 * @method static Builder|Movie query()
 * @method static Builder|Movie whereContent($value)
 * @method static Builder|Movie whereCreatedAt($value)
 * @method static Builder|Movie whereCurrentEpisode($value)
 * @method static Builder|Movie whereDescription($value)
 * @method static Builder|Movie whereFilter($params = [])
 * @method static Builder|Movie whereId($value)
 * @method static Builder|Movie whereIsPaid($value)
 * @method static Builder|Movie whereMaxEpisode($value)
 * @method static Builder|Movie whereOriginTitle($value)
 * @method static Builder|Movie wherePoster($value)
 * @method static Builder|Movie wherePublish()
 * @method static Builder|Movie whereRating($value)
 * @method static Builder|Movie whereRelease($value)
 * @method static Builder|Movie whereRuntime($value)
 * @method static Builder|Movie whereSlug($value)
 * @method static Builder|Movie whereStatus($value)
 * @method static Builder|Movie whereTaxonomy($taxonomy)
 * @method static Builder|Movie whereTaxonomyIn($taxonomies)
 * @method static Builder|Movie whereThumbnail($value)
 * @method static Builder|Movie whereTitle($value)
 * @method static Builder|Movie whereTrailerLink($value)
 * @method static Builder|Movie whereTvSeries($value)
 * @method static Builder|Movie whereUpdatedAt($value)
 * @method static Builder|Movie whereVideoQuality($value)
 * @method static Builder|Movie whereViews($value)
 * @method static Builder|Movie whereYear($value)
 * @mixin \Eloquent
 */
class Movie extends Model
{
    use PostTypeModel;

    protected $postType = 'movies';
    protected $fillable = [
        'title',
        'thumbnail',
        'origin_title',
        'description',
        'content',
        'poster',
        'rating',
        'release',
        'runtime',
        'video_quality',
        'trailer_link',
        'current_episode',
        'max_episode',
        'year',
        'status',
        'tv_series',
        'slug',
    ];

    protected $searchAttributes = [
        'title',
        'origin_title'
    ];

    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            /**
             * @var Movie $model
             */
            if ($model->year) {
                $year = Taxonomy::firstOrCreate([
                    'slug' => $model->year,
                    'taxonomy' => 'years',
                    'name' => $model->year,
                    'post_type' => 'movies',
                ]);

                $model->syncTaxonomy('years', [
                    'years' => [$year->id]
                ], 'movies');
            } else {
                $model->syncTaxonomy('years', [
                    'years' => []
                ], 'movies');
            }
        });
    }

    /**
     * Create Builder for frontend
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function selectFrontendBuilder()
    {
        $builder = self::createFrontendBuilder()
            ->select([
                'id',
                'title',
                'origin_title',
                'tv_series',
                'description',
                'thumbnail',
                'slug',
                'views',
                'year',
                'status',
                'video_quality',
            ]);

        return $builder;
    }

    public function fill(array $attributes)
    {
        if ($release = Arr::get($attributes, 'release')) {
            $attributes['year'] = explode('-', $release)[0];
        }

        return parent::fill($attributes);
    }

    public function genres()
    {
        return $this->taxonomies()
            ->where('taxonomy', '=', 'genres');
    }

    public function countries()
    {
        return $this->taxonomies()
            ->where('taxonomy', '=', 'countries');
    }

    public function downloadLinks()
    {
        return $this->hasMany(DownloadLink::class, 'movie_id', 'id');
    }

    public function movieRating()
    {
        return $this->hasMany(MovieRating::class, 'movie_id', 'id');
    }
    
    public function servers()
    {
        return $this->hasMany(VideoServer::class, 'movie_id', 'id');
    }
    
    public function getPoster()
    {
        if ($this->poster) {
            return upload_url($this->poster);
        }
        
        return $this->getThumbnail(false);
    }
    
    public function getTrailerLink()
    {
        if ($this->trailer_link) {
            return 'https://www.youtube.com/embed/' . get_youtube_id($this->trailer_link);
        }
        
        return '';
    }
    
    public function getServers($columns = ['id', 'name'])
    {
        return $this->servers()
            ->where('status', '=', 1)
            ->get($columns);
    }
    
    public function countRating()
    {
        return $this->movieRating()->count(['id']);
    }
    
    public function getStarRating()
    {
        $total = $this->movieRating()->sum('start');
        $count = $this->countRating();
        if ($count <= 0) {
            return 0;
        }
        return round($total * 5 / ($count * 5), 2);
    }
    
    public function getRelatedMovies(int $limit = 8)
    {
        $query = Movie::query();
        $query->select([
            'id',
            'name',
            'origin_title',
            'short_description',
            'thumbnail',
            'slug',
            'views',
            'release',
            'video_quality',
        ]);
    
        $query->wherePublish()
            ->where('id', '!=', $this->id);

        $genres = $this->taxonomies()
            ->where('taxonomy', '=', 'genres')
            ->pluck('id')
            ->toArray();

        if ($genres) {
            $query->whereHas('genres', function (Builder $q) use ($genres) {
                $q->whereIn('id', $genres);
            });
        } else {
            $query->whereRaw('1=2');
        }
        
        $query->limit($limit);
    
        return $query->get();
    }
}
