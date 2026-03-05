<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'name',
        'description',
    ];

    /* ── Relationships ───────────────────────────── */

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function services()
    {
        return $this->hasMany(ChatbotService::class, 'category_id')
            ->orderBy('name');
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    public function scopeForWebsite($query, $websiteId)
    {
        return $query->where('website_id', $websiteId);
    }
}
