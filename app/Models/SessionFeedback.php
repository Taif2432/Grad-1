<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionFeedback extends Model
{
    protected $table = 'session_feedbacks';
    protected $fillable = ['session_id','rating','comments'];

    public function session() 
    { return $this->belongsTo(Session::class); }
    
}

