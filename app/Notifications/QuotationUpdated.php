<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class QuotationUpdated extends Notification
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     */
    public function __construct($quotation)
    {
        $this->quotation = $quotation;
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
            'id' => $this->quotation->id,
            'data' => $this->customTemplate(),
            'read_at' => $this->quotation->read_at,
            'type' => $this->quotation->type,
            'created_at' => $this->quotation->created_at,
        ]);
    }

    public function customTemplate()
    {
        return [
            'id' => $this->quotation->id,
            'reference_no_value' => $this->quotation->reference_no_value,
            'sub_total_value' => $this->quotation->sub_total_value,
            'grand_total' => $this->quotation->grand_total,
            'currency_type' => $this->quotation->currency_type,
            'created_date' => $this->quotation->created_date,
            'expiry_date' => $this->quotation->expiry_date,
            'customized_client_details' => $this->quotation->customized_client_details,
            'quotation_id' => $this->quotation->quotation_id,
            'company_id' => $this->quotation->company_id,
            'company_branch_id' => $this->quotation->company_branch_id,
        ];
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}
