<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PO\PurchaseOrderMilenia;

class PoCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PurchaseOrderMilenia $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Purchase Order telah dibuat. No PO: ' . $this->purchaseOrder->no_po)
        ->view('email.po_created')
        ->with([
            'po' => $this->purchaseOrder,
        ]);
    }
}
