<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks;
    public $user;

    public function __construct($user, $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('Task Reminder')
                    ->view('emails.task_reminder')
                    ->with([
                        'tasks' => $this->tasks,
                        'user' => $this->user,
                    ]);
    }
}
