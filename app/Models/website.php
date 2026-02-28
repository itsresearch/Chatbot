<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'domain', 'api_key', 'welcome_message', 'widget_color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Generate a unique API key for this website.
     */
    public static function generateApiKey(): string
    {
        do {
            $key = 'miraai_' . bin2hex(random_bytes(20));
        } while (self::where('api_key', $key)->exists());

        return $key;
    }
}