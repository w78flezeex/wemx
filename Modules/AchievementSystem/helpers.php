<?php

if (!function_exists('checkAchievement')) {
    /**
     * Check and update achievement progress for a user
     */
    function checkAchievement($userId, $achievementType, $progress = 1): void
    {
        $achievement = \Modules\AchievementSystem\Entities\Achievement::where('requirements->type', $achievementType)
            ->where('is_active', true)
            ->first();

        if (!$achievement) {
            return;
        }

        $userAchievement = \Modules\AchievementSystem\Entities\UserAchievement::firstOrCreate([
            'user_id' => $userId,
            'achievement_id' => $achievement->id
        ]);

        $userAchievement->updateProgress($userAchievement->progress + $progress);
    }
}

if (!function_exists('getUserAchievements')) {
    /**
     * Get all achievements for a user
     */
    function getUserAchievements($userId)
    {
        return \Modules\AchievementSystem\Entities\UserAchievement::with('achievement.category')
            ->where('user_id', $userId)
            ->get();
    }
}

if (!function_exists('getUserAchievementPoints')) {
    /**
     * Get total achievement points for a user
     */
    function getUserAchievementPoints($userId): int
    {
        return \Modules\AchievementSystem\Entities\UserAchievement::where('user_id', $userId)
            ->whereNotNull('unlocked_at')
            ->join('module_achievements', 'module_user_achievements.achievement_id', '=', 'module_achievements.id')
            ->sum('module_achievements.points');
    }
}
