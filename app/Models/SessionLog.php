<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    protected $fillable = ['session_id', 'started_at', 'ended_at', 'notes'];

    public function session() {
        return $this->belongsTo(Session::class);
    }
}


