<?php

namespace App\Domains\Admin\Staff\Requests;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'name'          => ['required', 'regex:/^[\p{Devanagari}a-zA-Z\s]+$/u', 'string', 'max:255', new NoMultipleSpacesRule],
            'email'         => ['required','email','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i','unique:users,email,NULL,id,deleted_at,NULL'],
            'phone'         => [ 'required', 'numeric', 'regex:/^[1-9]\d{6,14}$/', 'unique:users,phone,NULL,id,deleted_at,NULL'],
            // 'password'      => ['required', 'string', 'min:8','confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'],
            'password'      => ['required', 'min:8','confirmed'],
            'role'          => ['required','exists:roles,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => trans('validation.only_characters', ['attribute' => strtolower(trans('cruds.staff.fields.name'))]),
            'phone.regex' => trans('validation.phone_regex'),
            // 'password.regex' => trans('validation.password.regex', ["attribute" => strtolower(trans('cruds.staff.fields.password'))])
        ];
    }

    public function attributes()
    {
        return [
            'name'      => strtolower(trans('cruds.staff.fields.name')),
            'phone'      => strtolower(trans('cruds.staff.fields.phone')),
            'email'     => strtolower(trans('cruds.staff.fields.email')),
            'password'  => strtolower(trans('cruds.staff.fields.password')),
            'role'      => strtolower(trans('cruds.staff.fields.role'))
        ];
    }
}
