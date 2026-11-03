<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'description',
        'condition',
        'points',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')->withTimestamps();
    }

    public function checkCondition($userData): bool
    {
        $condition = $this->condition;

        // Простая проверка условий
        if (str_contains($condition, 'total_savings')) {
            $threshold = (int) preg_replace('/[^0-9]/', '', $condition);
            return ($userData['total_savings'] ?? 0) >= $threshold;
        }

        if (str_contains($condition, 'habits_count')) {
            $threshold = (int) preg_replace('/[^0-9]/', '', $condition);
            return ($userData['habits_count'] ?? 0) >= $threshold;
        }

        if ($condition === 'first_calculation') {
            return ($userData['history_count'] ?? 0) >= 1;
        }

        if ($condition === 'dream_set') {
            return ($userData['has_dream'] ?? false) === true;
        }

        return false;
    }
}