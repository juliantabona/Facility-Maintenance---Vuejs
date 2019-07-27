<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class quotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $quotationPDF;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $quotation, $quotationPDF, $pdfName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->quotationPDF = $quotationPDF;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdfName = 'QUOTATION '.'#'.$this->quotation->id.' - '.Carbon::parse($this->quotation['created_at'])->format('M d Y') . '.pdf';

        return $this->subject($this->subject)
                    ->view('emails.send_quotation')
                    ->with(['msg' => $this->message])
                    ->attachData($this->quotationPDF->output(), $this->pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
