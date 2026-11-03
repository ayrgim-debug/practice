<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $histories = auth()->user()->histories()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('history.index', compact('histories'));
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        // Ищем только среди записей текущего пользователя
        $history = $request->user()->histories()->findOrFail($id);
        $history->delete();

        return response()->json(['success' => true]);
    }

    public function clear(Request $request): JsonResponse
    {
        $request->user()->histories()->delete();

        return response()->json(['success' => true]);
    }
}