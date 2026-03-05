<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $appends = ['file_url', 'is_image'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Whether this message has an attached file.
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Whether the attached file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        if (!$this->file_type) return false;
        return str_starts_with($this->file_type, 'image/');
    }

    /**
     * Generate a signed temporary URL for private file access.
     * Falls back to a route-based URL for non-S3 drivers.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return url('/chat-files/' . $this->id);
    }

    /**
     * Format file size for display.
     */
    public function formattedFileSize(): string
    {
        if (!$this->file_size) return '';
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    /**
     * Allowed file extensions for upload validation.
     */
    public static function allowedExtensions(): array
    {
        return [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            // Documents
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'rtf',
            // Archives
            'zip', 'rar', '7z',
        ];
    }

    /**
     * Max file size in bytes (10 MB).
     */
    public static function maxFileSize(): int
    {
        return 10 * 1024 * 1024;
    }
}