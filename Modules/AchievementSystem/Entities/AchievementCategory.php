<?php

namespace Modules\AchievementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AchievementCategory extends Model
{
    use HasFactory;

    protected $table = 'module_achievement_categories';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'category_id');
    }

    public function getActiveAchievements()
    {
        return $this->achievements()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
