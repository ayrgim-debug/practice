<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TipController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tips = $this->personalizedTips($user->id);
        $savedTipIds = $user->savedTips()->pluck('tip_id')->toArray();

        return view('tips.index', compact('tips', 'savedTipIds'));
    }

    /**
     * Возвращает список активных советов в порядке, уникальном для конкретного
     * пользователя (стабильный «перемешанный» порядок на основе его ID),
     * чтобы у разных аккаунтов подборка идей отличалась.
     */
    protected function personalizedTips(int $userId)
    {
        return Tip::active()->get()
            ->sortBy(function ($tip) use ($userId) {
                // Детерминированный "случайный" ключ: одинаков при каждом
                // заходе одного и того же пользователя, но свой для каждого юзера
                return crc32($userId . '-' . $tip->id);
            })
            ->values();
    }

    public function save($id): JsonResponse
    {
        $user = auth()->user();
        $tip = Tip::findOrFail($id);

        if ($user->savedTips()->where('tip_id', $tip->id)->exists()) {
            $user->savedTips()->detach($tip->id);
            $saved = false;
        } else {
            $user->savedTips()->attach($tip->id);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved,
        ]);
    }

    public function saved(): View
    {
        $user = auth()->user();
        $tips = $user->savedTips()->get();

        return view('tips.saved', compact('tips'));
    }
}