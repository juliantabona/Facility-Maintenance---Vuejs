<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InvoiceReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $invoice;
    public $receiptPDF;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $invoice, $receiptPDF)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->invoice = $invoice;
        $this->receiptPDF = $receiptPDF;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        $pdfName = 'RECEIPT For Invoice '.'#'.$this->invoice->id.' - '.Carbon::parse($this->invoice['created_at'])->format('M d Y') . '.pdf';

        return $this->subject($this->subject)
                    ->view('emails.send_invoice_receipt')
                    ->with(['invoice' => $this->invoice, 'msg' => $this->message])
                    ->attachData($this->receiptPDF->output(), $pdfName, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
