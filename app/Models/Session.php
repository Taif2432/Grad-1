<?php

namespace App\Models;

use App\Models\User; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'professional_id',
        'scheduled_at',
        'communication_type',
        'status',
        'is_anonymous',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
    public function client()
{
    return $this->belongsTo(User::class, 'client_id');
}

public function professional()
{
    return $this->belongsTo(User::class, 'professional_id');
}

    public function feedback()
{
    return $this->belongsTo(SessionFeedback::class, 'client_id');
}



}
