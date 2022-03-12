<?php

namespace App\Http\Requests;

use App\Models\Reply;
use App\Rules\SpamFree;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Gate;

class ReplyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply());
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
            'data.body' => ['required', new SpamFree]
        ];
    }
}
