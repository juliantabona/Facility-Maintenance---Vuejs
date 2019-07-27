<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $invoice;
    public $invoicePDF;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $invoice, $invoicePDF)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->invoice = $invoice;
        $this->invoicePDF = $invoicePDF;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdfName = 'INVOICE '.'#'.$this->invoice->id.' - '.Carbon::parse($this->invoice['created_at'])->format('M d Y') . '.pdf';

        return $this->subject($this->subject)
                    ->view('emails.send_invoice')
                    ->with(['msg' => $this->message])
                    ->attachData($this->invoicePDF->output(), $pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
