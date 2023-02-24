<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailBuilder;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $dataSend;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, $dataSend)
    {
        $this->users = $users;
        $this->dataSend = $dataSend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->users) {
            $mailBuilder = new MailBuilder($this->dataSend);
            foreach ($this->users as $email) {
                Mail::to($email)->send($mailBuilder);
            }
        }
    }
}
