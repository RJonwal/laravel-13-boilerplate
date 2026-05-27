<?php

namespace App\Domains\Admin\Role\Requests;

use App\Rules\NoMultipleSpacesRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
            'name'  => ['required', 'string', 'max:255', new NoMultipleSpacesRule, 'unique:roles,name,'.$this->role->id.',id,deleted_at,NULL'],
            'permissions'  => ['required', 'array'],
            'permissions.*'  => ['required', 'exists:permissions,id'],
        ];
    }

    public function attributes()
    {
        return [
            'name'           => strtolower(trans('cruds.role.fields.name')),
            'permission'     => strtolower(trans('cruds.role.fields.permission')),
            'permissions.*'  => strtolower(trans('cruds.role.fields.permission'))
        ];
    }
}
