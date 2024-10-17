<?php

namespace App\Http\Requests\task;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
                    'title'              => ['required', 'string','max:100'],
                    'description'        => ['required', 'string','max:300'],
                    'type'               => ['required','in:Bug,Feature,Improvement'],    
                    'priority'           => ['required','in:high,medium,low'],
                    'due_date'           => ['required','date'],
                    'task_dependency'    => ['nullable','array'],
                    'task_dependency.*'  => ['exists:tasks,id']
                    ];
    }

    public function passedValidation()
    {
        //the status should be open when created new task
        $this->merge([
            'status'   => 'Open'
        ]); 
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
