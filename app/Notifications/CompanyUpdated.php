<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CompanyUpdated extends Notification
{
    use Queueable;

    protected $company;

    /**
     * Create a new notification instance.
     */
    public function __construct($company)
    {
        $this->company = $company;
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
            'id' => $this->company->id,
            'data' => $this->customTemplate(),
            'read_at' => $this->company->read_at,
            'type' => $this->company->type,
            'created_at' => $this->company->created_at,
        ]);
    }

    public function customTemplate()
    {
        return [
            'id' => $this->company->id,
            'name' => $this->company->name,
            'logo' => $this->company->logo,
            'updated_at' => $this->company->created_at,
        ];
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}
