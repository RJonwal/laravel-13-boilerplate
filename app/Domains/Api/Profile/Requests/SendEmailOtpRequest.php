<?php

namespace App\Domains\Api\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use App\Domains\Core\User\Models\User;

class SendEmailOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = Auth::id();
        
        return [
            'email' => [
                'required',
                'email:dns',
                'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i',
                'unique:users,email,' . $userId . ',id,deleted_at,NULL',
                function ($attribute, $value, $fail) {
                    $authUser = Auth::user();

                    $exists = User::where('email', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(trans('api.validation.email.unique'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => trans('api.validation.email.required'),
            'email.email' => trans('api.validation.email.email'),
            'email.unique' => trans('api.validation.email.unique'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422)
        );
    }
}