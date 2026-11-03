<?php

namespace App\Http\Controllers;

use App\Http\Requests\DreamRequest;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(): View
    {
        $user = auth()->user();
        $stats = $this->statisticsService->getUserStatistics($user);
        $achievements = $user->achievements()->get();

        return view('dashboard.profile', compact('stats', 'achievements'));
    }

    public function updateDream(DreamRequest $request): JsonResponse
    {
        $user = auth()->user();

        $user->update([
            'dream_name' => $request->dream_name,
            'dream_price' => $request->dream_price,
        ]);

        $this->statisticsService->checkAchievements($user);

        return response()->json([
            'success' => true,
            'dream_name' => $user->dream_name,
            'dream_price' => $user->dream_price,
            'has_dream' => $user->hasDream(),
        ]);
    }

    public function getDream(): JsonResponse
    {
        $user = auth()->user();
        $totalSavings = $user->histories()->sum('total');
        $progress = 0;

        if ($user->hasDream() && $user->dream_price > 0) {
            $progress = min(($totalSavings / $user->dream_price) * 100, 100);
        }

        return response()->json([
            'dream_name' => $user->dream_name,
            'dream_price' => $user->dream_price,
            'total_savings' => $totalSavings,
            'progress' => round($progress, 1),
            'has_dream' => $user->hasDream(),
        ]);
    }
}