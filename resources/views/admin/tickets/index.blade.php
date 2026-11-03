@extends('layouts.app')

@section('title', 'Обращения')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
@endpush

@section('content')
    <div class="hero">
        <h2>📨 Обращения</h2>
        <p>Все сообщения пользователей в одном месте.</p>
    </div>

    @include('admin._nav')

    <form class="ad-filters" method="GET" action="{{ route('admin.tickets.index') }}">
        <select name="status">
            <option value="">Все статусы</option>
            @foreach(\App\Models\SupportTicket::STATUSES as $key => $label)
                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="type">
            <option value="">Все типы</option>
            @foreach(\App\Models\SupportTicket::TYPES as $key => $label)
                <option value="{{ $key }}" {{ $type === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <input type="text" name="q" value="{{ $search }}" placeholder="Поиск: тема, текст, имя, email">

        <button type="submit" class="sp-btn">🔍 Найти</button>
        @if($status || $type || $search !== '')
            <a href="{{ route('admin.tickets.index') }}" class="sp-btn sp-btn-secondary">Сбросить</a>
        @endif
    </form>

    <div class="ad-table-wrap">
        <table class="ad-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Дата</th>
                    <th>От кого</th>
                    <th>Тип</th>
                    <th>Тема</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->id }}</td>
                        <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            {{ $ticket->name }}
                            <div class="sp-meta">{{ $ticket->email }}</div>
                        </td>
                        <td>{{ $ticket->type_label }}</td>
                        <td><a class="ad-link" href="{{ route('admin.tickets.show', $ticket) }}">{{ \Illuminate\Support\Str::limit($ticket->subject, 60) }}</a></td>
                        <td><span class="st-badge st-{{ $ticket->status }}">{{ $ticket->status_label }}</span></td>
                        <td class="ad-actions">
                            <a class="sp-btn sp-btn-small" href="{{ route('admin.tickets.show', $ticket) }}">Открыть</a>
                            <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('Удалить обращение #{{ $ticket->id }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sp-btn sp-btn-small sp-btn-danger">✕</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-habits">🐷 Ничего не найдено.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tickets->links() }}
@endsection