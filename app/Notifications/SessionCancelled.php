<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Session;

class SessionCancelled extends Notification
{
    use Queueable;

    protected $session, $byRole;

    public function __construct($session,$byRole){$this->session=$session;$this->byRole=$byRole;}


    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable){
        return [
            'title'   => 'Session Cancelled',
            'message' => ucfirst($this->byRole).
                         " cancelled session for {$this->session->scheduled_at}",
        ];
    }
}
