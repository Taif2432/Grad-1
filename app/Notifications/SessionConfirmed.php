<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
 
class SessionConfirmed extends Notification
{
    use Queueable;
    protected $session;
    public function __construct($session){$this->session=$session;}
    public function via($notifiable){ return ['database']; }
    public function toDatabase($notifiable){
        return [
            'title'=>'Session Confirmed',
            'message'=>"Your session on {$this->session->scheduled_at} is confirmed.",
        ];
    }
}