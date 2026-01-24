<?php

namespace Juzaweb\Modules\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Juzaweb\Modules\Core\Enums\PostStatus;

class MovieRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'origin_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:190'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'tmdb_rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'release' => ['nullable', 'date'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'runtime' => ['nullable', 'string', 'max:255'],
            'video_quality' => ['nullable', 'string', 'max:255'],
            'trailer_link' => ['nullable', 'string', 'max:255'],
            'current_episode' => ['sometimes', 'integer', 'min:0'],
            'max_episode' => ['sometimes', 'integer', 'min:0'],
            'genres' => ['nullable', 'array'],
            'countries' => ['nullable', 'array'],
            'actors' => ['nullable', 'array'],
            'directors' => ['nullable', 'array'],
            'writers' => ['nullable', 'array'],
            'status' => ['required', Rule::enum(PostStatus::class)],
        ];
    }
}
