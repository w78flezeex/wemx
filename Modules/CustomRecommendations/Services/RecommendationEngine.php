<?php

namespace Modules\CustomRecommendations\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

class RecommendationEngine
{
    /**
     * Get personalized recommendations for a user
     */
    public function getRecommendations($userId, $limit = 5): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $preferences = $this->analyzeUserPreferences($userId);
        $recommendations = $this->generateRecommendations($preferences, $limit);

        return $recommendations;
    }

    /**
     * Analyze user behavior and preferences
     */
    public function analyzeBehavior($userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $orders = $user->orders()->with('package')->get();
        $preferences = [];

        // Analyze order history
        foreach ($orders as $order) {
            $package = $order->package;
            if ($package) {
                $categoryId = $package->category_id;
                $preferences['categories'][$categoryId] = ($preferences['categories'][$categoryId] ?? 0) + 1;
                $preferences['total_spent'] = ($preferences['total_spent'] ?? 0) + $order->price;
            }
        }

        // Analyze payment patterns
        $payments = $user->payments()->get();
        $preferences['payment_methods'] = $payments->pluck('gateway')->countBy()->toArray();

        return $preferences;
    }

    /**
     * Update user preferences based on new data
     */
    public function updatePreferences($userId, array $data): void
    {
        // Store or update user preferences
        DB::table('module_user_preferences')->updateOrInsert(
            ['user_id' => $userId],
            [
                'preferences' => json_encode($data),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Generate recommendations based on preferences
     */
    private function generateRecommendations(array $preferences, int $limit): array
    {
        $query = Package::with('category')
            ->where('status', 'active');

        // Filter by preferred categories
        if (isset($preferences['categories']) && !empty($preferences['categories'])) {
            $topCategories = array_keys(array_slice($preferences['categories'], 0, 3, true));
            $query->whereIn('category_id', $topCategories);
        }

        // Filter by price range if available
        if (isset($preferences['total_spent'])) {
            $avgSpent = $preferences['total_spent'] / max(count($preferences['categories'] ?? []), 1);
            $query->whereHas('prices', function ($q) use ($avgSpent) {
                $q->where('price', '<=', $avgSpent * 1.5);
            });
        }

        return $query->limit($limit)->get()->toArray();
    }

    /**
     * Analyze user preferences from various sources
     */
    private function analyzeUserPreferences($userId): array
    {
        $behavior = $this->analyzeBehavior($userId);
        
        // Get stored preferences
        $storedPreferences = DB::table('module_user_preferences')
            ->where('user_id', $userId)
            ->value('preferences');

        if ($storedPreferences) {
            $storedPreferences = json_decode($storedPreferences, true);
            $behavior = array_merge($behavior, $storedPreferences);
        }

        return $behavior;
    }
}
