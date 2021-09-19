<?php

namespace Juzaweb\Movie\Models\Movie;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Traits\PostTypeModel;
use Juzaweb\Movie\Models\DownloadLink;
use Illuminate\Database\Eloquent\Builder;

class Movie extends Model
{
    use PostTypeModel;

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
        'tv_series'
    ];

    protected $searchAttributes = [
        'title',
        'origin_title'
    ];

    public function fill(array $attributes)
    {
        if ($description = Arr::get($attributes, 'description')) {
            $attributes['short_description'] = sub_words(strip_tags($description), 15);
        }

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
        return $this->hasMany('Juzaweb\Movie\Models\Movie\MovieRating', 'movie_id', 'id');
    }
    
    public function servers()
    {
        return $this->hasMany('Juzaweb\Movie\Models\Video\VideoServer', 'movie_id', 'id');
    }
    
    public function getViews()
    {
        if ($this->views < 1000) {
            return $this->views;
        }
        
        return round($this->views / 1000, 1) . 'K';
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
            'other_name',
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
