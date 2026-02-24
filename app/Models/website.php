<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'domain', 'api_key'];

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}