<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InvoiceReceiptSent extends Notification
{
    use Queueable;

    protected $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     *
     * E.g) via mail, database, broadcast,  nexmo, and slack channels.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return $this->customTemplate();
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->invoice->id,
            'data' => $this->customTemplate(),
            'read_at' => $this->invoice->read_at,
            'type' => $this->invoice->type,
            'created_at' => $this->invoice->created_at,
        ]);
    }

    public function customTemplate()
    {
        return [
            'id' => $this->invoice->id,
            'reference_no_value' => $this->invoice->reference_no_value,
            'sub_total_value' => $this->invoice->sub_total_value,
            'grand_total' => $this->invoice->grand_total,
            'currency_type' => $this->invoice->currency_type,
            'created_date' => $this->invoice->created_date,
            'expiry_date' => $this->invoice->expiry_date,
            'customized_client_details' => $this->invoice->customized_client_details,
            'quotation_id' => $this->invoice->quotation_id,
            'company_id' => $this->invoice->company_id,
            'company_branch_id' => $this->invoice->company_branch_id,
        ];
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}
