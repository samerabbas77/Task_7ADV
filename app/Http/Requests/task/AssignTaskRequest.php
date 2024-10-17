<?php

namespace App\Http\Requests\task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssignTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }



    /**
     * before validate the request i will take the assign_to value and split it to two parts 
     * (first and last name)
     * then i added to request and compare validate it sipiritly
     * @return void
     */
        protected function prepareForValidation()
    {
        if ($this->assigned_to) {
            // Split the incoming full_name(assigned_to) into first and last names
            
            $names = explode(' ', $this->assigned_to);
            $this->merge([
                'firstName' => $names[0], // assuming first name is the first part
                'lastName' => isset($names[1]) ? $names[1] : null, // assuming last name is the second part
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return
            [
                'firstName'     => ['required','exists:user,firstName'],
                'lastName'      => ['required','exists:user,lastName']
            ];
    }

    public function messages(): array
    {
        return [
                'required' => 'The :attribute  is required',            
                'exists'      => 'The :attribute  must be exists in user table.',              
             ];
        
    }

    public function attributes(): array
    {
        return[
            'firstName'        => 'User Name',
            'lastName'        => 'User Name',

       ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }

}
