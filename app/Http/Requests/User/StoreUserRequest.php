<?php

namespace App\Http\Requests\User;


use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'firstName'    => 'required|string|max:255',
            'lastName'     => 'required|string|max:255',
            'email'        => 'required|email|unique:users|max:255',
            'password'     => 'required|string|confirmed|min:8',
            'role'         => ['required','in:admin,user']
        ];
    }

    public function messages(): array
    {
        return [
                'required' => 'The :attribute  is required',
                'string'   => 'The :attribute  must be string.',
                'email'      => 'The :attribute  must be email.',              
                'in'     => 'The :attribute  must be in one off the option.',
                'unique'     => 'The :attribute  must be unique.',
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
