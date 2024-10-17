<?php
namespace App\Http\Requests\task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddCommentRequest extends FormRequest
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
            'comment'  => ['required','string','max:200']
        ];
    }

    public function messages(): array
    {
        return [
                'required' => 'The :attribute  is required',            
                'string'      => 'The :attribute  must be string.',
                'max'         => 'The :attribute must not be more than :max characters.',
             ];
        
    }

    public function attributes(): array
    {
        return[
            'comment'        => 'Task Comment',

       ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }

}
