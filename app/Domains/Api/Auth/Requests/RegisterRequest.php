<?php

namespace App\Domains\Api\Auth\Requests;

use App\Http\Requests\ApiRequest;
use App\Rules\NoMultipleSpacesRule;

class RegisterRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'register_type' => ['required', 'in:normal,facebook,google,apple'],
            'name'  => ['required', 'regex:/^[a-zA-Z\s]+$/', 'string', 'max:255', new NoMultipleSpacesRule],
            'email'     => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','unique:users,email,NULL,id,deleted_at,NULL']            
        ];
        if($this->register_type == 'normal'){
            $rules['phone']  = ['required', 'regex:/^\+\d{10,15}$/', 'unique:users,phone,NULL,id,deleted_at,NULL'];
             
            $rules['password']  = ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'];
        }

        if(in_array($this->register_type, ['facebook', 'google', 'apple'])){
            $rules['social_user_id']  = ['required'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }
}
