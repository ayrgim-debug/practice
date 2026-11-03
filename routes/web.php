<?php

use App\Http\Controllers\{
    CalculatorController,
    HabitController,
    HistoryController,
    ProfileController,
    SupportController,
    TipController,
};
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ===== ГЛАВНАЯ СТРАНИЦА (ДОСТУПНА ВСЕМ) =====
Route::get('/', [CalculatorController::class, 'index'])->name('calculator');
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator');

// ===== РАСЧЁТ (ДОСТУПЕН ВСЕМ, ИСТОРИЯ ПИШЕТСЯ ТОЛЬКО АВТОРИЗОВАННЫМ) =====
Route::post('/calculator/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');

// ===== ПОДДЕРЖКА (ДОСТУПНА ВСЕМ) =====
Route::get('/support', [SupportController::class, 'index'])->name('support');
Route::post('/support', [SupportController::class, 'store'])->name('support.store')->middleware('throttle:5,1');

// ===== ГОСТЕВЫЕ МАРШРУТЫ =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ===== ВЫХОД =====
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ===== СОХРАНЕНИЕ ТЕМЫ =====
Route::post('/theme', function (Request $request) {
    $theme = $request->input('theme');
    if (in_array($theme, ['light', 'dark'])) {
        session(['theme' => $theme]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 400);
})->name('theme.save')->middleware('auth');

// ===== РЕДИРЕКТ СТАРОЙ ГЛАВНОЙ =====
Route::get('/dashboard', function () {
    return redirect()->route('calculator');
})->name('dashboard');

// ===== ЗАЩИЩЁННЫЕ МАРШРУТЫ =====
Route::middleware('auth')->group(function () {

    // ===== ИСТОРИЯ =====
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/history', [HistoryController::class, 'clear'])->name('history.clear');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy');

    // ===== ПРИВЫЧКИ =====
    Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::get('/habits/{id}/edit', [HabitController::class, 'edit'])->name('habits.edit');
    Route::put('/habits/{id}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/habits', [HabitController::class, 'clear'])->name('habits.clear');
    Route::delete('/habits/{id}', [HabitController::class, 'destroy'])->name('habits.destroy');
    Route::get('/habits/total', [HabitController::class, 'getTotal'])->name('habits.total');

    // ===== МЕЧТА =====
    Route::get('/dream', [ProfileController::class, 'getDream'])->name('dream.get');
    Route::post('/dream', [ProfileController::class, 'updateDream'])->name('dream.update');

    // ===== ПРОФИЛЬ =====
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // ===== СОВЕТЫ =====
    Route::get('/tips', [TipController::class, 'index'])->name('tips');
    Route::post('/tips/{id}/save', [TipController::class, 'save'])->name('tips.save');
    Route::get('/tips/saved', [TipController::class, 'saved'])->name('tips.saved');
});

// ===== АДМИН-ПАНЕЛЬ (ПОДДЕРЖКА) =====
require __DIR__ . '/admin.php';