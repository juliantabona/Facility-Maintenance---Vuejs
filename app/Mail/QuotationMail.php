<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class quotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $quotationPDF;
    public $pdfName;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $quotationPDF, $pdfName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->quotationPDF = $quotationPDF;
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
                    ->view('emails.send_quotation')
                    ->with(['msg' => $this->message])
                    ->attachData($this->quotationPDF->output(), $this->pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
