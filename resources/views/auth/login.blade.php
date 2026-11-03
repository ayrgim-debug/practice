@extends('layouts.guest')

@section('title', 'Вход')

@section('content')
    <div class="auth-form-container">
        <h2>🔑 Вход</h2>
        <p class="auth-desc">Войди в свою копилку и продолжай копить! 🐷</p>

        @if ($errors->any())
            <div class="auth-status error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="input-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@mail.ru" required autofocus />
            </div>

            <div class="input-group">
                <label for="password">🔒 Пароль</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required />
            </div>

            <!-- ===== ИСПРАВЛЕННЫЙ ЧЕКБОКС ===== -->
            <div class="input-group checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                    <span class="checkbox-text">Запомнить меня</span>
                </label>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; text-align:center; justify-content:center;">
                🔑 Войти
            </button>
        </form>

        <p class="auth-switch">
            Нет аккаунта? <a href="{{ route('register') }}">Зарегистрируйся</a>
        </p>
    </div>
@endsection

@push('styles')
<style>
    /* ===== ИСПРАВЛЕННЫЙ СТИЛЬ ДЛЯ ЧЕКБОКСА ===== */
    .checkbox-group {
        margin-bottom: 16px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-secondary);
        font-family: 'Fraunces', serif;
        font-size: 15px;
        user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #b8873b;
        cursor: pointer;
        flex-shrink: 0;
        margin: 0;
        border-radius: 4px;
        border: 2px solid var(--border);
        background: var(--bg-input);
        transition: all var(--transition);
    }

    .checkbox-label input[type="checkbox"]:checked {
        background: var(--gradient-main);
        border-color: #b8873b;
    }

    .checkbox-label input[type="checkbox"]:focus {
        outline: 2px solid rgba(184, 135, 59, 0.35);
        outline-offset: 2px;
    }

    .checkbox-text {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 15px;
        font-family: 'Fraunces', serif;
    }

    /* ===== ТЁМНАЯ ТЕМА ДЛЯ СТРАНИЦЫ ВХОДА ===== */
    body {
        background: #1a120b !important;
    }
    .auth-container {
        background: #2d1f14 !important;
        border-color: #4d3728 !important;
    }
    .auth-logo h1 {
        color: #b8873b !important;
    }
    .auth-subtitle {
        color: #b88a7a !important;
    }
    .auth-form-container h2 {
        color: #fdf4ed !important;
    }
    .auth-desc {
        color: #b88a7a !important;
    }
    .input-group label {
        color: #e8c98a !important;
    }
    .input-group input {
        background: #3d2b1c !important;
        border-color: #4d3728 !important;
        color: #fdf4ed !important;
    }
    .input-group input:focus {
        border-color: #b8873b !important;
        box-shadow: 0 0 0 6px rgba(184, 135, 59, 0.18) !important;
    }
    .auth-switch {
        color: #b88a7a !important;
    }
    .auth-switch a {
        color: #b8873b !important;
    }
    .checkbox-text {
        color: #e8c98a !important;
    }
    .checkbox-label input[type="checkbox"] {
        background: #3d2b1c !important;
        border-color: #4d3728 !important;
    }
    .checkbox-label input[type="checkbox"]:checked {
        background: var(--gradient-main) !important;
        border-color: #b8873b !important;
    }
</style>
@endpush