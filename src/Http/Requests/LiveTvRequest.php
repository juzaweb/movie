<?php

namespace Juzaweb\Modules\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiveTvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:190'],
            'streaming_url' => ['required', 'string', 'max:255'],
        ];
    }
}
