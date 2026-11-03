<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Http\Requests\HabitRequest;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(): View
    {
        $user = auth()->user();
        $habits = $user->habits()->orderBy('created_at', 'desc')->get();

        $total = $habits->sum(function ($habit) {
            return $habit->getYearlyTotal();
        });

        return view('habits.index', compact('habits', 'total'));
    }

    public function store(HabitRequest $request): JsonResponse
    {
        $user = auth()->user();

        $habit = $user->habits()->create([
            'name' => $request->name,
            'cost' => $request->cost,
            'frequency' => $request->frequency,
        ]);

        $this->statisticsService->checkAchievements($user);

        return response()->json([
            'success' => true,
            'habit' => $habit,
            'yearly' => $habit->getYearlyTotal(),
            'freq_label' => $habit->getFrequencyLabel(),
        ]);
    }

    public function edit($id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        return response()->json([
            'habit' => $habit,
            'frequencies' => Habit::getFrequencies(),
        ]);
    }

    public function update(HabitRequest $request, $id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $habit->update([
            'name' => $request->name,
            'cost' => $request->cost,
            'frequency' => $request->frequency,
        ]);

        return response()->json([
            'success' => true,
            'habit' => $habit,
            'yearly' => $habit->getYearlyTotal(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $habit->delete();

        return response()->json(['success' => true]);
    }

    public function clear(): JsonResponse
    {
        auth()->user()->habits()->delete();

        return response()->json(['success' => true]);
    }

    public function getTotal(): JsonResponse
    {
        $user = auth()->user();
        $total = $user->habits->sum(function ($habit) {
            return $habit->getYearlyTotal();
        });

        return response()->json([
            'total' => $total,
            'five_years' => $total * 5,
        ]);
    }
}