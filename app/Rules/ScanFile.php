<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\ValidationRule;

class ScanFile implements ValidationRule
{
    protected $file; // المتغير الذي سيحتفظ بالبيانات التي تمررها

    /**
     * قم بتمرير البيانات (مثل الملف) إلى قاعدة التحقق عبر الـ Constructor.
     *
     * @param  mixed  $file
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file; // تخزين الملف في الخاصية
    }

    /**
     * Handle a validation attempt.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate($attribute, $value, $fail):void
    {
        // إنشاء عميل GuzzleHttp جديد
        $client = new Client();

        // إرسال طلب POST إلى VirusTotal لفحص الملف
        $response = $client->request('POST', 'https://www.virustotal.com/vtapi/v2/file/scan', [
            'multipart' => [
                [
                    'name'     => 'apikey',
                    'contents' => env('CLAMAV_API_KEY'),
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($this->file->getPathname(), 'r'), // استخدام الملف الذي تم تمريره عبر الـ constructor
                ],
            ],
        ]);

        // الحصول على استجابة الطلب
        $responseBody = json_decode($response->getBody(), true);

        // التحقق مما إذا كان الملف مصابًا
        if ($responseBody['positives'] > 0) {
            $fail('The uploaded file is infected with a virus!');
        }
    }
}
