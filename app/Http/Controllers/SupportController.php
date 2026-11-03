<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportController extends Controller
{
    // Страница поддержки: форма + (для вошедших) список своих обращений
    public function index(Request $request): View
    {
        $myTickets = auth()->check()
            ? SupportTicket::where('user_id', auth()->id())->latest()->get()
            : collect();

        return view('support.index', [
            'myTickets' => $myTickets,
            'from' => Str::limit((string) $request->query('from', ''), 500, ''),
        ]);
    }

    // Отправка обращения
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'type' => ['required', Rule::in(array_keys(SupportTicket::TYPES))],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'page_url' => ['nullable', 'string', 'max:500'],
        ];

        if (!auth()->check()) {
            $rules['name'] = ['required', 'string', 'max:100'];
            $rules['email'] = ['required', 'email', 'max:150'];
        }

        $data = $request->validate($rules, [
            'type.required' => 'Выбери тип обращения.',
            'type.in' => 'Выбран неверный тип обращения.',
            'subject.required' => 'Укажи тему обращения.',
            'subject.min' => 'Тема слишком короткая (минимум 3 символа).',
            'subject.max' => 'Тема слишком длинная (максимум 150 символов).',
            'message.required' => 'Опиши проблему или вопрос.',
            'message.min' => 'Опиши подробнее (минимум 10 символов).',
            'message.max' => 'Сообщение слишком длинное (максимум 3000 символов).',
            'page_url.max' => 'Адрес страницы слишком длинный.',
            'name.required' => 'Укажи своё имя.',
            'name.max' => 'Имя слишком длинное.',
            'email.required' => 'Укажи email, чтобы мы могли ответить.',
            'email.email' => 'Введи корректный email.',
        ]);

        $user = auth()->user();

        SupportTicket::create([
            'user_id' => $user?->id,
            'name' => $user ? $user->name : $data['name'],
            'email' => $user ? $user->email : $data['email'],
            'type' => $data['type'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'page_url' => $data['page_url'] ?? null,
            'user_agent' => Str::limit((string) $request->userAgent(), 480, ''),
            'status' => 'new',
        ]);

        return redirect()->route('support')
            ->with('success', '✅ Спасибо! Обращение отправлено, мы скоро ответим.');
    }
}