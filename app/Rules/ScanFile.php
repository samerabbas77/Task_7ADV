<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class ScanFile implements ValidationRule
{
    protected $client;
    protected $errorMessage;

    public function __construct()
    {
        // Initialize the Guzzle client
        $this->client = new Client();
    }

    /**
     * Validate the file by scanning it using the VirusTotal API.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Read the API key from the .env file
            $apiKey = env('VIRUSTOTAL_API_KEY');
            
            // Prepare the file to be sent
            $file = fopen($value->getRealPath(), 'r');

            // Send the file to the VirusTotal API for scanning
            $response = $this->client->post('https://www.virustotal.com/vtapi/v2/file/scan', [
                'headers' => [
                    'x-apikey' => $apiKey,
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $file,
                    ],
                ],
            ]);

            // Decode the API response
            $result = json_decode($response->getBody()->getContents(), true);

            // You can perform further checks on the result here (e.g., check for positives)
            if ($result['response_code'] == 1) {
                // Success: Handle based on API response (you can check scan results, etc.)
                
            }

            $this->errorMessage = 'VirusTotal API scan failed: ' . $result['verbose_msg'];
            
        } catch (\Exception $e) {
            // Handle any exceptions (e.g., network issues, API errors)
            $this->errorMessage = 'Error scanning file: ' . $e->getMessage();
            
        }
    }

    /**
     * Get the error message for the validation failure.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage ?? 'The file failed the virus scan check.';
    }
}
