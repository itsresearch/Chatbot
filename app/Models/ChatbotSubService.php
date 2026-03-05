<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotSubService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'short_description',
        'detail_content',
    ];

    /* ── Relationships ───────────────────────────── */

    public function service()
    {
        return $this->belongsTo(ChatbotService::class, 'service_id');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
