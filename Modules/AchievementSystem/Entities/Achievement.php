<?php

namespace Modules\AchievementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'module_achievements';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'category_id',
        'points',
        'requirements',
        'is_hidden',
        'is_active',
        'sort_order',
        'data'
    ];

    protected $casts = [
        'requirements' => 'array',
        'data' => 'array',
        'is_hidden' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AchievementCategory::class);
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function isUnlockedByUser($userId): bool
    {
        return $this->userAchievements()
            ->where('user_id', $userId)
            ->where('unlocked_at', '!=', null)
            ->exists();
    }

    public function getProgressForUser($userId): array
    {
        $userAchievement = $this->userAchievements()
            ->where('user_id', $userId)
            ->first();

        if (!$userAchievement) {
            return ['current' => 0, 'required' => 1, 'percentage' => 0];
        }

        $current = $userAchievement->progress ?? 0;
        $required = $this->requirements['count'] ?? 1;
        $percentage = min(100, ($current / $required) * 100);

        return [
            'current' => $current,
            'required' => $required,
            'percentage' => round($percentage, 2)
        ];
    }
}
