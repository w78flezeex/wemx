<?php

namespace Modules\AchievementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AchievementSystem\Entities\Achievement;
use Modules\AchievementSystem\Entities\AchievementCategory;

class AchievementController extends Controller
{
    /**
     * Display a listing of achievements
     */
    public function index()
    {
        $categories = AchievementCategory::with(['achievements' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return view('achievement-system::achievements.index', compact('categories'));
    }

    /**
     * Display the specified achievement
     */
    public function show(Achievement $achievement)
    {
        $achievement->load('category');
        
        return view('achievement-system::achievements.show', compact('achievement'));
    }

    /**
     * Display user achievements
     */
    public function userAchievements($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $achievements = getUserAchievements($userId);
        $totalPoints = getUserAchievementPoints($userId);

        return view('achievement-system::achievements.user', compact('user', 'achievements', 'totalPoints'));
    }
}
