<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PR\PurchaseRequest;
use App\Models\User;

class PurchaseRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $purchaseRequest;
    public $user;

    public function __construct(PurchaseRequest $purchaseRequest, $user)
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
        return $this->from('admin.ga@ccas.co.id')
        ->subject('Purchase Request Milenia Group dengan No.PR ' . $this->purchaseRequest->no_pr)
            ->view('email.purchase_request')
            ->with(['pr' => $this->purchaseRequest,'user' => $this->user]);
    }
}
