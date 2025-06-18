<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('session_.{sessionId}', function ($user, $sessionId) {
    return true; // allow all authenticated users (for now)
});