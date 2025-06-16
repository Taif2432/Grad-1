<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = ['professional_id', 'available_date', 'start_time', 'end_time'];

    public function professional() {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
