<?php

namespace Modules\AchievementSystem\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAchievement extends Model
{
    use HasFactory;

    protected $table = 'module_user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'progress',
        'unlocked_at',
        'data'
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function isUnlocked(): bool
    {
        return $this->unlocked_at !== null;
    }

    public function unlock()
    {
        $this->unlocked_at = now();
        $this->save();

        // Add points to user
        $this->user->increment('achievement_points', $this->achievement->points);
    }

    public function updateProgress($progress)
    {
        $this->progress = $progress;
        
        // Check if achievement should be unlocked
        if ($progress >= ($this->achievement->requirements['count'] ?? 1)) {
            $this->unlock();
        } else {
            $this->save();
        }
    }
}
