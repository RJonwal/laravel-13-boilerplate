<?php

namespace App\Domains\Api\Profile\Requests;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
    //     $userId = Auth::id();
    //     $user = Auth::user();
        
    //     // Check if user has host role
    //     $hostRoleId = config('constant.roles.host', 3);
    //     $isHost = $user->roles->contains('id', $hostRoleId);
        
    //     $rules = [
    //         'name' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'string', 'max:255'],
    //         'instagram_handle' => ['required', 'string', 'max:255'],
    //         'tiktok' => ['nullable', 'string', 'max:255'],
    //         'profile_image' => ['nullable', 'image', 'max:2048'],
    //     ];
        
    //     // Add venue fields only for host role
    //     if ($isHost) {
    //         $rules['company_name'] = ['required', 'string', 'max:255'];
    //         $rules['venue_name'] = ['required', 'string', 'max:255'];
    //         $rules['venue_type'] = ['required', 'exists:venue_types,uuid,deleted_at,NULL'];
    //         $rules['venue_description'] = ['required', 'string', 'max:1000'];
    //     }
        
    //     return $rules;
    // }

    // /**
    //  * Get custom validation messages
    //  *
    //  * @return array
    //  */
    // public function messages(): array
    // {
    //     return [
    //         'name.required' => trans('api.validation.name.required'),
    //         'name.regex' => trans('api.validation.name.regex'),
    //         'instagram_handle.required' => trans('api.validation.instagram_handle.required'),
    //         'venue_name.required' => trans('api.validation.venue_name.required'),
    //         'venue_type.required' => trans('api.validation.venue_type.required'),
    //         'venue_type.integer' => trans('api.validation.venue_type.integer'),
    //         'venue_type.min' => trans('api.validation.venue_type.min'),
    //         'venue_description.required' => trans('api.validation.venue_description.required'),
    //         'venue_description.max' => trans('api.validation.venue_description.max'),
    //         'profile_image.image' => trans('api.validation.profile_image.image'),
    //     ];
    // }

    public function rules(): array
    {
        $user = Auth::user();

        $hostRoleId   = config('constant.roles.host', 3);
        $memberRoleId = config('constant.roles.member', 4);

        $isHost   = $user->roles->contains('id', $hostRoleId);
        $isMember = $user->roles->contains('id', $memberRoleId);

        $rules = [
            'name' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'string', 'max:255'],
            'instagram_handle' => ['required', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],

        ];

        // 🔹 HOST → single image
        if ($isHost) {
            $rules['profile_image'] = ['nullable', 'image', 'max:'.config('constant.profile_max_size')];

            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['venue_name'] = ['required', 'string', 'max:255'];
            $rules['venue_type'] = ['required', 'exists:venue_types,uuid,deleted_at,NULL'];
            $rules['venue_description'] = ['required', 'string', 'max:1000'];
        }

        // 🔹 MEMBER → minimum 3 images
        // if ($isMember) {
        //     $rules['profile_images'] = ['required', 'array', 'min:3', 'max:6'];
        //     $rules['profile_images.*'] = ['image', 'max:'.config('constant.profile_max_size')];
        //     $rules['featured_index'] = ['required_with:profile_images', 'integer', 'min:0'];
        // }
        if ($isMember) {
            $rules['profile_images'] = ['nullable', 'array', 'max:6'];
            $rules['profile_images.*'] = ['image', 'max:' . config('constant.profile_max_size')];
            $rules['featured_index'] = ['nullable', 'integer', 'min:0'];
            $rules['deleted_image_ids'] = ['nullable', 'array'];
            $rules['deleted_image_ids.*'] = ['integer', 'exists:uploads,id'];
            // CITY VALIDATION
            $rules['event_location_id'] = ['required','exists:event_locations,id'];
        }


        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $user = Auth::user();

            $memberRoleId = config('constant.roles.member', 4);

            if(! $user->roles->contains('id', $memberRoleId)){
                return;
            }

            $existingCount = $user->profileImages()->count();

            $deletedCount = $this->has('deleted_image_ids')
                ? count($this->deleted_image_ids)
                : 0;

            $newUploads = $this->hasFile('profile_images')
                ? count($this->file('profile_images'))
                : 0;

            $finalTotal = $existingCount - $deletedCount + $newUploads;

            if ($finalTotal < 3) {
                $validator->errors()->add(
                    'profile_images',
                    'At least 3 profile images are required.'
                );
            }

            // featured index range check
            if ($this->hasFile('profile_images') && $this->has('featured_index')) {
        

                    $user = Auth::user();

                    $existingCount = $user->profileImages()->count();

                    $deletedCount = $this->filled('deleted_image_ids')
                        ? count($this->deleted_image_ids)
                        : 0;

                    $newUploads = $this->hasFile('profile_images')
                        ? count($this->file('profile_images'))
                        : 0;

                    $finalTotal = $existingCount - $deletedCount + $newUploads;

                    if ($this->featured_index < 0 || $this->featured_index >= $finalTotal) {
                        $validator->errors()->add(
                            'featured_index',
                            'Featured index must be within total profile images range.'
                        );
                    }
    
            }
        });
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