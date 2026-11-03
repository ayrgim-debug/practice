<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;

class AchievementService
{
    public function getUserAchievements(User $user): array
    {
        $achievements = $user->achievements()->get();

        $all = Achievement::all();

        $result = [];

        foreach ($all as $achievement) {
            $unlocked = $achievements->contains('id', $achievement->id);

            $result[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'icon' => $achievement->icon ?? '🏆',
                'description' => $achievement->description,
                'points' => $achievement->points,
                'unlocked' => $unlocked,
                'unlocked_at' => $unlocked ? $achievements->firstWhere('id', $achievement->id)->pivot->created_at : null,
            ];
        }

        return $result;
    }

    public function getTotalPoints(User $user): int
    {
        return $user->achievements()->sum('points');
    }

    public function getUnlockedCount(User $user): int
    {
        return $user->achievements()->count();
    }

    public function getTotalAchievements(): int
    {
        return Achievement::count();
    }
}