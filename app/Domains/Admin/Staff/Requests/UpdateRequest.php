<?php

namespace App\Domains\Admin\Staff\Requests;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $userUuid = $this->route('staff');
        return [
            'name'              => ['required', 'regex:/^[\p{Devanagari}a-zA-Z\s]+$/u', 'string', 'max:255', new NoMultipleSpacesRule],
            'email'             => ['required','email:dns','regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'unique:users,email,'. $userUuid.',uuid,deleted_at,NULL'],
            'phone'             => [ 'required', 'numeric', 'regex:/^[1-9]\d{6,14}$/', 'unique:users,phone,'. $userUuid.',uuid,deleted_at,NULL'],

            'role'   => ['required','exists:roles,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => trans('validation.only_characters', ['attribute' => strtolower(trans('cruds.staff.fields.name'))]),
            // 'password.regex' => trans('validation.password.regex', ["attribute" => strtolower(trans('cruds.staff.fields.password'))]),
            'phone.regex'    => trans('validation.phone_regex'),
        ];
    }

    public function attributes()
    {
        return [
            'name'      => strtolower(trans('cruds.staff.fields.name')),
            'email'     => strtolower(trans('cruds.staff.fields.email')),
            'phone'     => strtolower(trans('cruds.staff.fields.phone')),
            'password'  => strtolower(trans('cruds.staff.fields.password')),
            'role'      => strtolower(trans('cruds.staff.fields.role'))
        ];
    }
}
