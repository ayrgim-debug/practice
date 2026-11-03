<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    // Список обращений с фильтрами и поиском
    public function index(Request $request): View
    {
        $query = SupportTicket::with('user')->latest();

        $status = $request->query('status');
        if ($status && array_key_exists($status, SupportTicket::STATUSES)) {
            $query->where('status', $status);
        }

        $type = $request->query('type');
        if ($type && array_key_exists($type, SupportTicket::TYPES)) {
            $query->where('type', $type);
        }

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($w) use ($like) {
                $w->where('subject', 'like', $like)
                    ->orWhere('message', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'status', 'type', 'search'));
    }

    // Просмотр одного обращения
    public function show(SupportTicket $ticket): View
    {
        $ticket->load('user');

        return view('admin.tickets.show', compact('ticket'));
    }

    // Смена статуса и ответ пользователю
    public function update(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))],
            'admin_reply' => ['nullable', 'string', 'max:3000'],
        ], [
            'status.required' => 'Выбери статус.',
            'status.in' => 'Выбран неверный статус.',
            'admin_reply.max' => 'Ответ слишком длинный (максимум 3000 символов).',
        ]);

        $reply = trim((string) ($data['admin_reply'] ?? ''));
        $replyChanged = $reply !== '' && $reply !== (string) $ticket->admin_reply;

        $ticket->status = $data['status'];
        $ticket->admin_reply = $reply !== '' ? $reply : null;

        if ($replyChanged) {
            $ticket->replied_at = now();
        }

        $ticket->save();

        return back()->with('success', '✅ Обращение обновлено.');
    }

    public function destroy(SupportTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', '🗑️ Обращение удалено.');
    }
}