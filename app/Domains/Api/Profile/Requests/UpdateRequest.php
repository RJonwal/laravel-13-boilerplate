<?php

namespace App\Domains\Api\Profile\Requests;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'name'  => ['required', 'regex:/^[a-zA-Z\s]+$/', 'string', 'max:255', new NoMultipleSpacesRule],
            // 'email'     => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','unique:users,email,NULL,id,deleted_at,NULL'],
            'country_code'  => ['required', 'string', 'regex:/^\+\d{1,4}$/'],
            'phone'  => ['required', 'numeric', 'regex:/^[0-9]{7,15}$/', 'unique:users,phone,NULL,id,deleted_at,NULL'], 
            "profile_image" => ['nullable', 'image', 'max:'.config('constant.profile_max_size')],

        ];


        return $rules;
    }


    // /**
    //  * Get custom validation messages
    //  *
    //  * @return array
    //  */
    public function messages(): array
    {
        return [
            'name.required' => trans('api.validation.name.required'),
            'name.regex' => trans('api.validation.name.regex'),
            'instagram_handle.required' => trans('api.validation.instagram_handle.required'),
            'venue_name.required' => trans('api.validation.venue_name.required'),
            'venue_type.required' => trans('api.validation.venue_type.required'),
            'venue_type.integer' => trans('api.validation.venue_type.integer'),
            'venue_type.min' => trans('api.validation.venue_type.min'),
            'venue_description.required' => trans('api.validation.venue_description.required'),
            'venue_description.max' => trans('api.validation.venue_description.max'),

            // HOST
            'profile_image.image' => trans('api.validation.profile_image.image'),

            // MEMBER
            'profile_images.required' => 'At least 3 profile images are required.',
            'profile_images.array' => 'Profile images must be an array.',
            'profile_images.min' => 'At least 3 profile images are required.',
            'profile_images.*.image' => 'Each file must be an image.',
            'profile_images.*.max' => 'Each image must not exceed '.config('constant.profile_max_size_in_mb').' KB.',
            'profile_images.max' => 'You can upload a maximum of 6 images.',

        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
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