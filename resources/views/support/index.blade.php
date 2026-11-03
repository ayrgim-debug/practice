@extends('layouts.app')

@section('title', 'Поддержка')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/support.css') }}">
@endpush

@section('content')
    <div class="hero">
        <h2>🆘 Поддержка</h2>
        <p>Что-то сломалось или есть вопрос? Расскажи нам — мы всё посмотрим и ответим. 🐷</p>
    </div>

    <div class="sp-card">
        <h3>✉️ Новое обращение</h3>

        @if($errors->any())
            <div class="sp-alert">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form class="sp-form" method="POST" action="{{ route('support.store') }}">
            @csrf

            @guest
                <div class="sp-row">
                    <div class="sp-field">
                        <label for="spName">Твоё имя</label>
                        <input type="text" id="spName" name="name" value="{{ old('name') }}" maxlength="100" placeholder="Как тебя зовут?">
                    </div>
                    <div class="sp-field">
                        <label for="spEmail">Email для ответа</label>
                        <input type="email" id="spEmail" name="email" value="{{ old('email') }}" maxlength="150" placeholder="name@example.com">
                    </div>
                </div>
            @endguest

            <div class="sp-row">
                <div class="sp-field">
                    <label for="spType">Тип обращения</label>
                    <select id="spType" name="type">
                        @foreach(\App\Models\SupportTicket::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', 'bug') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sp-field">
                    <label for="spSubject">Тема</label>
                    <input type="text" id="spSubject" name="subject" value="{{ old('subject') }}" maxlength="150" placeholder="Коротко: что случилось?">
                </div>
            </div>

            <div class="sp-field">
                <label for="spMessage">Описание</label>
                <textarea id="spMessage" name="message" maxlength="3000" placeholder="Что ты делал, что ожидал увидеть и что произошло на самом деле?">{{ old('message') }}</textarea>
                <div class="sp-hint"><span id="spCounter">0</span> / 3000</div>
            </div>

            <div class="sp-field">
                <label for="spPage">Страница, где возникла проблема (необязательно)</label>
                <input type="text" id="spPage" name="page_url" value="{{ old('page_url', $from) }}" maxlength="500" placeholder="https://...">
            </div>

            <div>
                <button type="submit" class="sp-btn">📨 Отправить</button>
            </div>
        </form>
    </div>

    @auth
        <div class="sp-card">
            <h3>📬 Мои обращения</h3>

            @forelse($myTickets as $ticket)
                <div class="sp-ticket">
                    <div class="sp-ticket-head">
                        <strong>{{ $ticket->subject }}</strong>
                        <span class="st-badge st-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
                    </div>
                    <div class="sp-meta">
                        {{ $ticket->type_label }} · {{ $ticket->created_at->format('d.m.Y H:i') }}
                    </div>
                    <div class="sp-text">{{ $ticket->message }}</div>

                    @if($ticket->admin_reply)
                        <div class="sp-reply">
                            <div class="sp-reply-title">💬 Ответ поддержки
                                @if($ticket->replied_at)
                                    <span>· {{ $ticket->replied_at->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                            <div class="sp-text">{{ $ticket->admin_reply }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-habits">🐷 Ты пока ничего не отправлял.</div>
            @endforelse
        </div>
    @else
        <div class="sp-note">
            💡 Войди в аккаунт, и здесь появятся твои обращения с ответами поддержки.
        </div>
    @endauth
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('spMessage');
        const counter = document.getElementById('spCounter');
        if (!textarea || !counter) return;

        const update = () => { counter.textContent = textarea.value.length; };
        textarea.addEventListener('input', update);
        update();
    });
</script>
@endpush