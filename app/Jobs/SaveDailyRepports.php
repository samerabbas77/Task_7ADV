<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LogReport;
use Illuminate\Bus\Queueable;
use App\Mail\TaskReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
         // Set the target due date to two days before the current date
         $targetDate = Carbon::now()->subDays(2);

         // Find all users with tasks that meet the criteria
         $users = User::whereHas('assignedTasks', function ($query) use ($targetDate) {
             $query->whereIn('status', ['Open', 'In_Progress'])
                    ->whereDate('due_date', '=', $targetDate);
                    })->with(['assignedTasks' => function ($query) use ($targetDate) {
                        $query->whereIn('status', ['Open', 'In_Progress'])
                            ->whereDate('due_date', '=', $targetDate);
                    }])->get();
    
         // Send the email to each user
         foreach ($users as $user) {
             $tasks = $user->tasks;
             Mail::to($user->email)->send(new TaskReminderMail($user, $tasks));
         }
     }
  

   
}
