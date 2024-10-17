<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
          
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|string|confirmed|min:8',
        ];
    }
    
    protected function   failedValidation(\Illuminate\contracts\Validation\Validator $validator) 
    { 
        throw new HttpResponseException(response()->json([ 
        'status'=>'error', 
        'message'=>'Please check the input', 
        'errors'=>$validator->errors(), 
        ])); 
    } 
}