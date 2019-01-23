<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InvoiceCreated extends Notification
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
        return [
            'invoice' => $this->invoice,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->invoice->id,
            'data' => [
                'invoice' => $this->invoice,
                ],
            'read_at' => $this->invoice->read_at,
            'type' => $this->invoice->type,
            'created_at' => $this->invoice->created_at,
        ]);
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}
