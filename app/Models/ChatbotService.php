<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotService extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    /* ── Relationships ───────────────────────────── */

    public function category()
    {
        return $this->belongsTo(ChatbotCategory::class, 'category_id');
    }

    public function subServices()
    {
        return $this->hasMany(ChatbotSubService::class, 'service_id')
            ->orderBy('name');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
