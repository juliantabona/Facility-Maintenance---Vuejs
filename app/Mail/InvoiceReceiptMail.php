<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $invoice;
    public $receiptPDF;
    public $pdfName;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $invoice, $receiptPDF, $pdfName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->invoice = $invoice;
        $this->receiptPDF = $receiptPDF;
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
                    ->view('emails.send_invoice_receipt')
                    ->with(['invoice' => $this->invoice, 'msg' => $this->message])
                    ->attachData($this->receiptPDF->output(), $this->pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
