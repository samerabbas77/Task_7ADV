<?php

namespace App\Http\Requests\task;

use App\Rules\ScanFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadFileRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',  
                'mimetypes:image/jpeg,image/png,image/gif/webp,
                           application/pdf,
                           application/msword,
                           application/vnd.openxmlformats-officedocument.wordprocessingml.document' ,  
                'max:5120',
                new ScanFile($this->file('file')) //virus check
                ]
            ];
    }


    public function messages()
    {
        return [
            'file.required' => 'تحميل الملف مطلوب.',
            'file.file' => 'يجب أن يكون الملف صالحًا.',
            'file.mimes' => 'نوع الملف غير مدعوم. الرجاء تحميل الملفات بصيغ jpg, jpeg, png, pdf, doc, docx.',
            'file.max' => 'الحد الأقصى لحجم الملف هو 5 ميغابايت.',
        ];
    }

    public function attributes()
    {
        return [
            'file' => 'ملف التحميل',
        ];
    }

    protected function failedValidation(Validator $Validator){
        $errors = $Validator->errors()->all();
        throw new HttpResponseException($this->error($errors,'Validation error',422));
    }
}
