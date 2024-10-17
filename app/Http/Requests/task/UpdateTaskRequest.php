<?php

namespace App\Http\Requests\task;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
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
                    'title'              => ['nullable', 'string','max:100'],
                    'description'        => ['nullable', 'string','max:300'],
                    'type'               => ['nullable','in: Bug, Feature, Improvement'],               
                    'priority'           => ['nullable','in: hight,medium,low'],
                    'due_date'           => ['nullable','date'],
                    'task_dependency'    => ['nullable|array'],
                    'task_dependency.*'  => ['exists:user,id']
                ];
    }

    public function messages(): array
    {
        return [
                'required' => 'The :attribute  is required',
                'string'   => 'The :attribute  must be string.',
                'max.title'   => 'The :attribute  must be at max 100',
                'max.description'   => 'The :attribute  must be at max 300',
                'in'     => 'The :attribute  must be in one off the option.',
                'unique'     => 'The :attribute  must be unique.',
                'date'      => 'The :attribute  must be date.',              
                'exists'      => 'The :attribute  must be exists in user table.',              
             ];
        
    }

    public function attributes(): array
    {
        return[
            'firstName'        => 'User Name',
            'lastName'        => 'User Name',
            'title'  => 'Task Title',
            'description'  => 'Task Description ',
            'type'  => 'Task Type',
            'status'  => 'Task status' ,
            'priority'  => 'Task priority',
       ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }

}
