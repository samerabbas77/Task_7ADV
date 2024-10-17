<?php

namespace App\Http\Requests\task;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskStatusRequest extends FormRequest
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
            'status' => 'required'|'in:Open,In_Progress, Completed, Blocked'
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

            'status'  => 'Task status' ,
        ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }
}
