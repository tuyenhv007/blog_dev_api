<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailBuilder extends Mailable
{
    use Queueable, SerializesModels;

    public $emailInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($sendMailData)
    {
        $this->emailInfo = $sendMailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = 'template.email.' . $this->emailInfo['template'];
        return $this->from(env("MAIL_USERNAME"))
            ->subject($this->emailInfo['subject'])
            ->view($template, [
                'data' => $this->emailInfo['data']
            ]);
    }
}
