<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'App\User' => 'App\User',
]);

class UserUpdated extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            'id' => $this->user->id,
            'data' => $this->customTemplate(),
            'read_at' => $this->user->read_at,
            'type' => $this->user->type,
            'created_at' => $this->user->created_at,
        ]);
    }

    public function customTemplate()
    {
        return [
            'id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'company_branch_id' => $this->user->company_branch_id,
            'company_id' => $this->user->company_id,
        ];
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}
