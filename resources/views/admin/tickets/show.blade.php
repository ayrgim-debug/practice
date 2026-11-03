@extends('layouts.app')

@section('title', 'Обращение #' . $ticket->id)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
@endpush

@section('content')
    <div class="hero">
        <h2>📨 Обращение #{{ $ticket->id }}</h2>
        <p><a class="ad-link" href="{{ route('admin.tickets.index') }}">← Ко всем обращениям</a></p>
    </div>

    @include('admin._nav')

    @if($errors->any())
        <div class="sp-alert">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="sp-card">
        <div class="sp-ticket-head">
            <h3 style="margin:0;">{{ $ticket->subject }}</h3>
            <span class="st-badge st-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
        </div>

        <dl class="ad-meta">
            <dt>Тип</dt>
            <dd>{{ $ticket->type_label }}</dd>

            <dt>От кого</dt>
            <dd>
                {{ $ticket->name }} ·
                <a class="ad-link" href="mailto:{{ $ticket->email }}">{{ $ticket->email }}</a>
                @if($ticket->user)
                    <span class="sp-meta">(аккаунт #{{ $ticket->user->id }})</span>
                @else
                    <span class="sp-meta">(гость)</span>
                @endif
            </dd>

            <dt>Отправлено</dt>
            <dd>{{ $ticket->created_at->format('d.m.Y H:i') }}</dd>

            <dt>Страница</dt>
            <dd>
                @if($ticket->safe_page_url)
                    <a class="ad-link" href="{{ $ticket->safe_page_url }}" target="_blank" rel="noopener noreferrer">{{ $ticket->safe_page_url }}</a>
                @elseif($ticket->page_url)
                    {{ $ticket->page_url }}
                @else
                    <span class="sp-meta">не указана</span>
                @endif
            </dd>

            <dt>Браузер</dt>
            <dd class="sp-meta">{{ $ticket->user_agent ?: 'неизвестно' }}</dd>
        </dl>

        <div class="sp-text sp-message">{{ $ticket->message }}</div>
    </div>

    <div class="sp-card">
        <h3>💬 Ответ и статус</h3>

        <form class="sp-form" method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
            @csrf
            @method('PATCH')

            <div class="sp-field">
                <label for="adStatus">Статус</label>
                <select id="adStatus" name="status">
                    @foreach(\App\Models\SupportTicket::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $ticket->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sp-field">
                <label for="adReply">Ответ пользователю (он увидит его на странице «Поддержка»)</label>
                <textarea id="adReply" name="admin_reply" maxlength="3000" placeholder="Напиши ответ...">{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
                @if($ticket->replied_at)
                    <div class="sp-hint">Последний ответ: {{ $ticket->replied_at->format('d.m.Y H:i') }}</div>
                @endif
            </div>

            <div class="ad-form-actions">
                <button type="submit" class="sp-btn">💾 Сохранить</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" style="margin-top: 14px;" onsubmit="return confirm('Удалить это обращение навсегда?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="sp-btn sp-btn-danger">🗑️ Удалить обращение</button>
        </form>
    </div>
@endsection