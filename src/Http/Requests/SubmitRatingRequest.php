<?php

namespace Juzaweb\Modules\Movie\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRatingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'movie_id' => ['required', 'exists:movies,id'],
            'star' => ['required', 'numeric', 'min:0', 'max:5'],
        ];
    }
}
