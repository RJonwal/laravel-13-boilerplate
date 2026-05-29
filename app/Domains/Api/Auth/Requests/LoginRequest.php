<?php

namespace App\Domains\Api\Auth\Requests;

use App\Http\Requests\ApiRequest;

class LoginRequest extends ApiRequest
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
            'login_type' => ['required', 'in:normal,facebook,google,apple'],            
        ];
        if($this->login_type == 'normal'){
            $rules['user_login'] = ['required'];
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['social_user_id']  = ['required'];
        }
        return $rules;
    }
}
