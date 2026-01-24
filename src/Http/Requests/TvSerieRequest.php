<?php

namespace Juzaweb\Modules\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TvSerieRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'origin_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:190'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'release' => ['nullable', 'date'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'runtime' => ['nullable', 'string', 'max:255'],
            'video_quality' => ['nullable', 'string', 'max:255'],
            'trailer_link' => ['nullable', 'string', 'max:255'],
            'current_episode' => ['nullable', 'integer', 'min:0'],
            'max_episode' => ['nullable', 'integer', 'min:0'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:movie_genres,id'],
            'countries' => ['nullable', 'array'],
            'countries.*' => ['exists:movie_countries,id'],
            'actors' => ['nullable', 'array'],
            'actors.*' => ['exists:movie_actors,id'],
            'directors' => ['nullable', 'array'],
            'directors.*' => ['exists:movie_directors,id'],
            'writers' => ['nullable', 'array'],
            'writers.*' => ['exists:movie_writers,id'],
        ];
    }
}
