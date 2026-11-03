<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Первый шаг',
                'icon' => '🎯',
                'description' => 'Сделал первый расчёт в Копи-Копилке',
                'condition' => 'first_calculation',
                'points' => 10,
            ],
            [
                'name' => 'Экономный',
                'icon' => '💰',
                'description' => 'Накопил 10 000 ₽',
                'condition' => 'total_savings >= 10000',
                'points' => 20,
            ],
            [
                'name' => 'Копильщик',
                'icon' => '🐷',
                'description' => 'Накопил 50 000 ₽',
                'condition' => 'total_savings >= 50000',
                'points' => 30,
            ],
            [
                'name' => 'Мастер сбережений',
                'icon' => '🏆',
                'description' => 'Накопил 100 000 ₽',
                'condition' => 'total_savings >= 100000',
                'points' => 50,
            ],
            [
                'name' => 'Собиратель привычек',
                'icon' => '📋',
                'description' => 'Добавил 5 привычек',
                'condition' => 'habits_count >= 5',
                'points' => 15,
            ],
            [
                'name' => 'Мечтатель',
                'icon' => '⭐',
                'description' => 'Установил мечту',
                'condition' => 'dream_set',
                'points' => 10,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}