@extends('layouts.app')

@section('title', 'Админ-панель')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
@endpush

@section('content')
    <div class="hero">
        <h2>⚙️ Админ-панель</h2>
        <p>Обращения пользователей: ошибки, вопросы и предложения.</p>
    </div>

    @include('admin._nav')

    <div class="ad-stats">
        <a class="ad-stat" href="{{ route('admin.tickets.index', ['status' => 'new']) }}">
            <div class="ad-stat-num accent">{{ $stats['new'] }}</div>
            <div class="ad-stat-label">🆕 Новые</div>
        </a>
        <a class="ad-stat" href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}">
            <div class="ad-stat-num">{{ $stats['in_progress'] }}</div>
            <div class="ad-stat-label">🔧 В работе</div>
        </a>
        <a class="ad-stat" href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}">
            <div class="ad-stat-num">{{ $stats['resolved'] }}</div>
            <div class="ad-stat-label">✅ Решено</div>
        </a>
        <a class="ad-stat" href="{{ route('admin.tickets.index', ['status' => 'closed']) }}">
            <div class="ad-stat-num">{{ $stats['closed'] }}</div>
            <div class="ad-stat-label">📁 Закрыто</div>
        </a>
        <a class="ad-stat" href="{{ route('admin.tickets.index', ['type' => 'bug']) }}">
            <div class="ad-stat-num danger">{{ $stats['open_bugs'] }}</div>
            <div class="ad-stat-label">🐞 Открытых ошибок</div>
        </a>
        <div class="ad-stat">
            <div class="ad-stat-num">{{ $stats['users'] }}</div>
            <div class="ad-stat-label">👥 Пользователей</div>
        </div>
    </div>

    <div class="sp-card">
        <h3>🕒 Последние обращения</h3>

        @forelse($latest as $ticket)
            <a class="ad-latest" href="{{ route('admin.tickets.show', $ticket) }}">
                <div class="ad-latest-main">
                    <strong>{{ $ticket->subject }}</strong>
                    <span class="sp-meta">{{ $ticket->type_label }} · {{ $ticket->name }} · {{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <span class="st-badge st-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
            </a>
        @empty
            <div class="empty-habits">🐷 Обращений пока нет.</div>
        @endforelse

        @if($stats['total'] > 0)
            <div style="margin-top: 14px;">
                <a class="sp-btn sp-btn-secondary" href="{{ route('admin.tickets.index') }}">Все обращения ({{ $stats['total'] }})</a>
            </div>
        @endif
    </div>
@endsection