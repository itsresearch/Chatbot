<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = ['website_id', 'visitor_token'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}