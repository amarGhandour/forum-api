<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThreadStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.title' => ['required', 'string'],
            'data.body' => ['required', 'string'],
            'data.slug' => ['required', Rule::unique('threads', 'slug')],
            'data.channel_id' => ['required', Rule::exists('channels', 'id')]
        ];
    }
}
