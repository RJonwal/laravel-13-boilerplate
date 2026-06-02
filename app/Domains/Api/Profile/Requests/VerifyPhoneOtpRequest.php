<?php

namespace App\Domains\Api\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use App\Domains\Core\User\Models\User;

class VerifyPhoneOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = Auth::id();
        
        return [
            'phone' => [
                'required', 
                'regex:/^\+\d{10,15}$/',
                'unique:users,phone,' . $userId . ',id,deleted_at,NULL',
                function ($attribute, $value, $fail) {
                    $authUser = Auth::user();

                    $exists = User::where('phone', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(trans('api.validation.phone.unique'));
                    }
                },
            ],
            'otp' => ['required', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => trans('api.validation.phone.required'),
            'phone.regex' => trans('api.validation.phone.regex'),
            'phone.unique' => trans('api.validation.phone.unique'),
            'otp.required' => 'OTP is required',
            'otp.size' => 'OTP must be 6 digits',
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