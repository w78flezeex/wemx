<?php

namespace Modules\AIChatbot\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'module_chat_messages';

    protected $fillable = [
        'session_id',
        'content',
        'type',
        'metadata',
        'is_ai_generated'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_ai_generated' => 'boolean'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id', 'session_id');
    }

    public function isFromUser(): bool
    {
        return $this->type === 'user';
    }

    public function isFromAI(): bool
    {
        return $this->type === 'ai';
    }

    public function isSystem(): bool
    {
        return $this->type === 'system';
    }

    public function getMetadata($key = null, $default = null)
    {
        if ($key === null) {
            return $this->metadata;
        }

        return data_get($this->metadata, $key, $default);
    }
}
