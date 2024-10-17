<?php

namespace App\Http\Requests\User;


use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    use ApiResponseTrait;
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
            'firstName'    => 'nullable|string|max:255',
            'lastName'     => 'nullable|string|max:255',
            'email'        => 'nullable|email|unique:users|max:255',
            'password'     => 'nullable|string|confirmed|min:8',
            'role'         => ['nullable','in:admin,user']
        ];
    }

    public function messages(): array
    {
        return [
                'nullable' => 'The :attribute  is nullable',
                'string'   => 'The :attribute  must be string.',
                'email'      => 'The :attribute  must be email.',              
                'in'     => 'The :attribute  must be in one off the option.',
             ];
        
    }

    public function attributes(): array
    {
        return[
            'firstName'        => 'User title',
            'lastName'        => 'User title',
            'email'           => 'User E-mail',
            'password'        => 'User Password',
            'role'            => 'User Role',
          
       ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }
}
