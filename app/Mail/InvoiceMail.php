<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $invoicePDF;
    public $pdfName;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $invoicePDF, $pdfName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->invoicePDF = $invoicePDF;
        $this->pdfName = $pdfName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.send_invoice')
                    ->with(['msg' => $this->message])
                    ->attachData($this->invoicePDF->output(), $this->pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
