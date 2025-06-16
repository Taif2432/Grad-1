<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Content extends Model
{
    use HasFactory;

     protected $fillable = ['title', 'description', 'type', 'file_path','professional_id'];

    public function contentType()
{
    return $this->belongsTo(ContentType::class);
}
    public function categories()
{
    return $this->belongsToMany(Category::class);
}

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
