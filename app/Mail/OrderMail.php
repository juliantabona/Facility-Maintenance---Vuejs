<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $order;
    public $invoice;
    public $orderPDF;
    public $invoicePDF;
    public $bankDetailsPDF;
    public $mailConfig;

    /**
     * Create a new message instance.
     */
    public function __construct( $subject, $message, $order, $invoice, $orderPDF, $invoicePDF, $bankDetailsPDF, $mailConfig)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->order = $order;
        $this->invoice = $invoice;
        
        //  Order PDF Details
        $this->orderPDF = $orderPDF;
        //  Invoice PDF Details 
        $this->invoicePDF = $invoicePDF;
        //  Bank Account PDF Details
        $this->bankDetailsPDF = $bankDetailsPDF;
        //  Config to help us know which PDF's to attach.
        $this->mailConfig = $mailConfig;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject('Order #' . $this->order->id .' - '.  $this->subject)
                ->view('emails.send_order')
                ->with([
                    'message' => $this->message, 'order' => $this->orderPDF, 
                    'invoice' => $this->invoicePDF, 'bankAccount' => $this->bankDetailsPDF, 'mailConfig' => $this->mailConfig
                ]);

        //  Check the mail configurations if we can attach the order PDF
        if($this->mailConfig['attach_order_pdf']){
            
            $orderPDF_Name = 'ORDER '.'#'.$this->order->id.' - '.Carbon::parse($this->order['created_at'])->format('M d Y') . '.pdf';

            $email->attachData($this->orderPDF->output(), $orderPDF_Name, [
                'mime' => 'application/pdf',
            ]);
        }

        //  Check the mail configurations if we can attach the invoice PDF
        if($this->mailConfig['attach_invoice_pdf']){
            
            $invoicePDF_Name = 'INVOICE '.'#'.$this->invoice->id.' - '.Carbon::parse($this->invoice['created_at'])->format('M d Y') . '.pdf';

            $email->attachData($this->invoicePDF->output(), $invoicePDF_Name, [
                'mime' => 'application/pdf',
            ]);
        }

        //  Check the mail configurations if we can attach the bank account details PDF
        if($this->mailConfig['attach_bank_details_pdf']){
            
            $bankDetailsPDF_Name = 'bank_account_details.pdf';

            $email->attachData($this->bankDetailsPDF->output(), $bankDetailsPDF_Name, [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
