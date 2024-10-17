<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LogReport;
use Carbon\Carbon;

class SaveDailyRepports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $logFile = storage_path('logs/laravel.log');
        $logLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($logLines as $line) 
        {
            // suppose the line is this style:

            // [2024-10-16 10:32:45] local.INFO: This is an info message
            
            // extract the Date
            preg_match('/\[(.*?)\]/', $line, $dateMatch);
            $date = isset($dateMatch[1]) ? Carbon::parse($dateMatch[1]) : null;

            // extract type (info, error, warning, etc)
            preg_match('/\.([A-Z]+):/', $line, $typeMatch);
            $type = isset($typeMatch[1]) ? strtolower($typeMatch[1]) : 'unknown';

            // extract message details
            $details = substr($line, strpos($line, ':') + 2);    

            // save the data in the database
            LogReport::create([
                'details' => $details,
                'date' => $date,
                'type' => $type,
            ]);
        }

    }
}
