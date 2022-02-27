<?php

namespace App\Http\Requests;

use App\Models\Thread;
use App\Rules\SpamFree;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Gate;
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
        return Gate::allows('create', new Thread());
    }

    protected function failedAuthorization()
    {
        throw new ThrottleRequestsException();
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
            'data.slug' => ['required', Rule::unique('threads', 'slug')],
            'data.channel_id' => ['required', Rule::exists('channels', 'id')]
        ];
    }
}
