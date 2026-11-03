<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use Illuminate\Support\Facades\Log;

class StatisticsService
{
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function getUserStatistics(User $user): array
    {
        $totalSavings = $user->histories()->sum('total');
        $habitsCount = $user->habits()->count();
        $historyCount = $user->histories()->count();

        $dreamProgress = 0;
        $hasDream = $user->hasDream();

        if ($hasDream && $user->dream_price > 0) {
            $dreamProgress = min(($totalSavings / $user->dream_price) * 100, 100);
        }

        return [
            'total_savings' => round($totalSavings, 2),
            'habits_count' => $habitsCount,
            'history_count' => $historyCount,
            'has_dream' => $hasDream,
            'dream_name' => $user->dream_name,
            'dream_price' => $user->dream_price,
            'dream_progress' => round($dreamProgress, 1),
            'achievements_count' => $user->achievements()->count(),
            'member_since' => $user->created_at->format('d.m.Y'),
        ];
    }

    public function checkAchievements(User $user): void
    {
        $stats = $this->getUserStatistics($user);

        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            if ($this->checkCondition($achievement->condition, $stats)) {
                $user->achievements()->attach($achievement->id);
                Log::info('Achievement unlocked', [
                    'user_id' => $user->id,
                    'achievement' => $achievement->name,
                ]);
            }
        }
    }

    protected function checkCondition(string $condition, array $stats): bool
    {
        if (str_contains($condition, 'total_savings')) {
            $threshold = (int) preg_replace('/[^0-9]/', '', $condition);
            return ($stats['total_savings'] ?? 0) >= $threshold;
        }

        if (str_contains($condition, 'habits_count')) {
            $threshold = (int) preg_replace('/[^0-9]/', '', $condition);
            return ($stats['habits_count'] ?? 0) >= $threshold;
        }

        if ($condition === 'first_calculation') {
            return ($stats['history_count'] ?? 0) >= 1;
        }

        if ($condition === 'dream_set') {
            return ($stats['has_dream'] ?? false) === true;
        }

        return false;
    }

    public function getLeaderboard(int $limit = 10): array
    {
        $users = User::withCount('habits', 'histories')
            ->withSum('histories', 'total')
            ->orderBy('histories_sum_total', 'desc')
            ->limit($limit)
            ->get();

        return $users->map(function ($user) {
            return [
                'name' => $user->name,
                'total_savings' => round($user->histories_sum_total ?? 0, 2),
                'habits_count' => $user->habits_count,
                'history_count' => $user->histories_count,
            ];
        })->toArray();
    }
}