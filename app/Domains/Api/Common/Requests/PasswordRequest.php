<?php
namespace App\Domains\Api\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;


class PasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // only logged-in users can hit this
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*\d)(?=.*[@$!%*#?&]).+$/u',
            ],
            'confirm_new_password' => ['required', 'string', 'same:new_password'],
        ];
    }

    public function attributes(): array
    {
        return [
            'old_password' => __('auth.fields.old_password'),
            'new_password' => __('auth.fields.new_password'),
            'confirm_new_password' => __('auth.fields.confirm_new_password'),
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.regex' => __('auth.messages.update_password.validation.regex'),
            'confirm_new_password.same' => __('auth.messages.update_password.validation.confirm'),
            'old_password.required' => __('auth.messages.update_password.validation.old_required'),
        ];
    }
}