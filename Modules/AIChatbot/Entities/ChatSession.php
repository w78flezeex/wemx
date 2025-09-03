<?php

namespace Modules\AIChatbot\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatSession extends Model
{
    use HasFactory;

    protected $table = 'module_chat_sessions';

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'context',
        'data'
    ];

    protected $casts = [
        'context' => 'array',
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id', 'session_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function addMessage($content, $type = 'user', $metadata = []): ChatMessage
    {
        return $this->messages()->create([
            'content' => $content,
            'type' => $type,
            'metadata' => $metadata
        ]);
    }

    public function getContext(): array
    {
        return $this->context ?? [];
    }

    public function updateContext(array $context): void
    {
        $this->context = array_merge($this->getContext(), $context);
        $this->save();
    }
}
