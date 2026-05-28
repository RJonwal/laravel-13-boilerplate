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
            'contact_number' => [
                'required', 
                'regex:/^\+\d{10,15}$/',
                'unique:users,contact_number,' . $userId . ',id,deleted_at,NULL',
                function ($attribute, $value, $fail) {
                    $authUser = Auth::user();

                    $exists = User::where('contact_number', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail(trans('api.validation.contact_number.unique'));
                    }
                },
            ],
            'otp' => ['required', 'string', 'size:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.required' => trans('api.validation.contact_number.required'),
            'contact_number.regex' => trans('api.validation.contact_number.regex'),
            'contact_number.unique' => trans('api.validation.contact_number.unique'),
            'otp.required' => 'OTP is required',
            'otp.size' => 'OTP must be 4 digits',
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