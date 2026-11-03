<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use App\Models\Habit;
use App\Models\History;
use App\Services\StatisticsService;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;
    protected AchievementService $achievementService;

    public function __construct(
        StatisticsService $statisticsService,
        AchievementService $achievementService
    ) {
        $this->statisticsService = $statisticsService;
        $this->achievementService = $achievementService;
    }

    public function index(): View
    {
        $user = auth()->user();

        $stats = $this->statisticsService->getUserStatistics($user);

        $achievements = $this->achievementService->getUserAchievements($user);

        $recentHistory = $user->histories()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $habits = $user->habits()->get();

        $tips = Tip::active()->ordered()->limit(4)->get();

        return view('dashboard.index', compact(
            'stats',
            'achievements',
            'recentHistory',
            'habits',
            'tips'
        ));
    }
}