<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseRequest;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($purchaseRequest, $user)
    {
        $this->purchaseRequest = $purchaseRequest;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Purchase Request Approved')
            ->view('email.pr_approved')->with([
                'pr'   => $this->purchaseRequest,
                'user' => $this->user,
            ]);
    }
}
