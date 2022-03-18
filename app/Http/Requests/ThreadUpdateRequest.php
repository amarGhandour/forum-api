<?php

namespace App\Http\Requests;

use App\Rules\SpamFree;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThreadUpdateRequest extends FormRequest
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
            'data.title' => ['required', 'string', new SpamFree],
            'data.body' => ['required', 'string', new SpamFree],
            'data.channel_id' => ['required', Rule::exists('channels', 'id')]
        ];
    }
}
