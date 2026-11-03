<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Tip;
use App\Services\CalculatorService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    protected CalculatorService $calculatorService;
    protected StatisticsService $statisticsService;

    public function __construct(
        CalculatorService $calculatorService,
        StatisticsService $statisticsService
    ) {
        $this->calculatorService = $calculatorService;
        $this->statisticsService = $statisticsService;
    }

    public function index(): View
    {
        $user = auth()->user();

        // Если пользователь авторизован — показываем статистику
        if ($user) {
            $stats = $this->statisticsService->getUserStatistics($user);
            $recentHistory = $user->histories()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            // Для гостей — пустые данные
            $stats = [
                'total_savings' => 0,
                'habits_count' => 0,
                'history_count' => 0,
                'achievements_count' => 0,
                'has_dream' => false,
                'dream_name' => null,
                'dream_price' => 0,
                'dream_progress' => 0,
            ];
            $recentHistory = collect();
        }

        $tips = Tip::active()->ordered()->limit(4)->get();

        return view('dashboard.calculator', compact('stats', 'recentHistory', 'tips'));
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'cost' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'integer', 'in:365,260,104,52,12'],
        ]);

        $cost = (float) $request->cost;
        $days = (int) $request->frequency;

        $result = $this->calculatorService->calculate($cost, $days);

        // Сохраняем в историю только для авторизованных
        $user = auth()->user();
        if ($user) {
            History::create([
                'user_id' => $user->id,
                'cost' => $cost,
                'days' => $days,
                'total' => $result['yearly'],
            ]);
        }

        return response()->json($result);
    }
}