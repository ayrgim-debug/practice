@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="hero">
        <h2>👤 Мой профиль</h2>
        <p>Здесь ты можешь посмотреть свою статистику и достижения! 🐷</p>
    </div>

    <div class="profile-container">
        <!-- ===== АВАТАР И ИМЯ ===== -->
        <div class="profile-header">
            <div class="profile-avatar">
                {{ auth()->user()->name[0] ?? 'К' }}
            </div>
            <div class="profile-name">{{ auth()->user()->name }}</div>
            <div class="profile-email">{{ auth()->user()->email }}</div>
            <div class="profile-badge">🐷 В Копи-Копилке с {{ $stats['member_since'] ?? 'недавно' }}</div>
        </div>

        <!-- ===== СТАТИСТИКА ===== -->
        <div class="profile-stats">
            <div class="profile-stat-card">
                <div class="profile-stat-icon">📋</div>
                <div class="profile-stat-number">{{ $stats['habits_count'] ?? 0 }}</div>
                <div class="profile-stat-label">Привычек</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-icon">📜</div>
                <div class="profile-stat-number">{{ $stats['history_count'] ?? 0 }}</div>
                <div class="profile-stat-label">Расчётов</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-icon">💰</div>
                <div class="profile-stat-number">{{ number_format($stats['total_savings'] ?? 0, 0, '', ' ') }}</div>
                <div class="profile-stat-label">Экономия в год (₽)</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-icon">🏆</div>
                <div class="profile-stat-number">{{ $stats['achievements_count'] ?? 0 }}</div>
                <div class="profile-stat-label">Достижений</div>
            </div>
        </div>

        <!-- ===== МЕЧТА ===== -->
        @if($stats['has_dream'] ?? false)
            <div class="profile-dream">
                <div class="profile-dream-header">
                    <span class="profile-dream-icon">🎯</span>
                    <h4>Моя мечта</h4>
                </div>
                <div class="profile-dream-name">{{ $stats['dream_name'] }}</div>
                <div class="profile-dream-price">{{ number_format($stats['dream_price'] ?? 0, 0, '', ' ') }} ₽</div>
                <div class="profile-dream-bar">
                    <div class="profile-dream-fill" style="width: {{ $stats['dream_progress'] ?? 0 }}%;"></div>
                </div>
                <div class="profile-dream-progress">
                    {{ $stats['dream_progress'] ?? 0 }}% от цели
                </div>
            </div>
        @else
            <div class="profile-dream-empty">
                <p>🐷 У тебя пока нет мечты!</p>
                <p style="font-size:14px; color:var(--text-muted);">Перейди в раздел <strong>«Копилка»</strong> и установи свою мечту!</p>
            </div>
        @endif

        <!-- ===== ДОСТИЖЕНИЯ ===== -->
        @if(isset($achievements) && $achievements->count() > 0)
            <div class="profile-achievements">
                <h4>🏆 Мои достижения</h4>
                <div class="profile-achievements-grid">
                    @foreach($achievements as $achievement)
                        <div class="profile-achievement-card {{ $achievement['unlocked'] ? 'unlocked' : 'locked' }}">
                            <div class="profile-achievement-icon">{{ $achievement['icon'] ?? '🏆' }}</div>
                            <div class="profile-achievement-name">{{ $achievement['name'] }}</div>
                            <div class="profile-achievement-desc">{{ $achievement['description'] }}</div>
                            @if($achievement['unlocked'])
                                <div class="profile-achievement-badge">✅ Получено</div>
                            @else
                                <div class="profile-achievement-badge locked">🔒 Закрыто</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ===== КНОПКА ВЫХОДА ===== -->
        <div class="profile-actions">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-secondary" style="width:100%; border-color:#b33b2c; color:#b33b2c;">
                    🚪 Выйти из аккаунта
                </button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* ===== ПРОФИЛЬ ===== */
    .profile-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .profile-header {
        text-align: center;
        padding: 30px 20px 20px;
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        border: 2px solid var(--border);
        margin-bottom: 24px;
        transition: all var(--transition);
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--gradient-main);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 36px;
        font-weight: 900;
        font-family: 'Fraunces', serif;
        margin: 0 auto 12px;
        box-shadow: 0 8px 32px rgba(184, 135, 59, 0.35);
    }

    .profile-name {
        font-size: 24px;
        font-weight: 900;
        font-family: 'Fraunces', serif;
        color: var(--text-primary);
    }

    .profile-email {
        color: var(--text-muted);
        font-size: 16px;
        font-weight: 600;
        margin-top: 4px;
    }

    .profile-badge {
        display: inline-block;
        margin-top: 10px;
        background: var(--gradient-gold);
        color: #8a611f;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 18px;
        border-radius: 60px;
        font-family: 'Fraunces', serif;
    }

    /* ===== СТАТИСТИКА ===== */
    .profile-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .profile-stat-card {
        background: var(--bg-input);
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 20px 16px;
        text-align: center;
        transition: all var(--transition);
    }

    .profile-stat-card:hover {
        transform: translateY(-4px);
        border-color: #b8873b;
        box-shadow: var(--shadow-sm);
    }

    .profile-stat-icon {
        font-size: 28px;
        margin-bottom: 4px;
    }

    .profile-stat-number {
        font-size: 28px;
        font-weight: 900;
        color: #b8873b;
        font-family: 'Fraunces', serif;
    }

    .profile-stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        font-family: 'Fraunces', serif;
        margin-top: 2px;
    }

    /* ===== МЕЧТА ===== */
    .profile-dream {
        background: var(--bg-input);
        border: 2px solid #b8873b;
        border-radius: var(--radius-sm);
        padding: 24px 28px;
        margin-bottom: 24px;
        transition: all var(--transition);
    }

    .profile-dream:hover {
        border-color: #b8873b;
        box-shadow: 0 0 30px rgba(184, 135, 59, 0.12);
    }

    .profile-dream-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .profile-dream-icon {
        font-size: 24px;
    }

    .profile-dream-header h4 {
        font-size: 17px;
        font-weight: 800;
        font-family: 'Fraunces', serif;
        color: var(--text-secondary);
        margin: 0;
    }

    .profile-dream-name {
        font-size: 22px;
        font-weight: 900;
        font-family: 'Fraunces', serif;
        color: #b8873b;
        margin-bottom: 2px;
    }

    .profile-dream-price {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .profile-dream-bar {
        width: 100%;
        height: 18px;
        background: var(--border);
        border-radius: 60px;
        overflow: hidden;
        transition: background var(--transition);
    }

    .profile-dream-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 60px;
        transition: width 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 0 20px rgba(184, 135, 59, 0.35);
    }

    .profile-dream-progress {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 8px;
        font-family: 'Fraunces', serif;
        text-align: right;
    }

    .profile-dream-empty {
        background: var(--bg-input);
        border: 2px dashed var(--border);
        border-radius: var(--radius-sm);
        padding: 30px 20px;
        text-align: center;
        margin-bottom: 24px;
        transition: all var(--transition);
    }

    .profile-dream-empty p {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-secondary);
        font-family: 'Fraunces', serif;
    }

    /* ===== ДОСТИЖЕНИЯ ===== */
    .profile-achievements {
        margin-bottom: 24px;
    }

    .profile-achievements h4 {
        font-size: 18px;
        font-weight: 800;
        font-family: 'Fraunces', serif;
        color: var(--text-secondary);
        margin-bottom: 14px;
    }

    .profile-achievements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
    }

    .profile-achievement-card {
        background: var(--bg-input);
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 16px 14px;
        text-align: center;
        transition: all var(--transition);
    }

    .profile-achievement-card.unlocked {
        border-color: #2f5233;
        background: rgba(52, 211, 153, 0.05);
    }

    .profile-achievement-card.locked {
        opacity: 0.5;
        filter: grayscale(0.5);
    }

    .profile-achievement-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-sm);
    }

    .profile-achievement-icon {
        font-size: 32px;
        margin-bottom: 4px;
    }

    .profile-achievement-name {
        font-size: 14px;
        font-weight: 800;
        font-family: 'Fraunces', serif;
        color: var(--text-primary);
    }

    .profile-achievement-desc {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    .profile-achievement-badge {
        display: inline-block;
        margin-top: 8px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 12px;
        border-radius: 60px;
        font-family: 'Fraunces', serif;
    }

    .profile-achievement-badge:not(.locked) {
        background: #2f5233;
        color: #064e3b;
    }

    .profile-achievement-badge.locked {
        background: var(--border);
        color: var(--text-muted);
    }

    /* ===== КНОПКА ВЫХОДА ===== */
    .profile-actions {
        margin-top: 8px;
    }

    .profile-actions .btn-secondary {
        padding: 14px;
        font-size: 16px;
        font-weight: 700;
        font-family: 'Fraunces', serif;
        border-color: #b33b2c;
        color: #b33b2c;
        background: transparent;
        transition: all var(--transition);
        cursor: pointer;
    }

    .profile-actions .btn-secondary:hover {
        background: #b33b2c;
        color: white;
        transform: scale(1.02);
    }

    /* ===== АДАПТИВ ===== */
    @media (max-width: 600px) {
        .profile-stats {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .profile-achievements-grid {
            grid-template-columns: 1fr 1fr;
        }

        .profile-dream {
            padding: 18px 16px;
        }

        .profile-dream-name {
            font-size: 18px;
        }

        .profile-stat-number {
            font-size: 22px;
        }
    }

    @media (max-width: 400px) {
        .profile-stats {
            grid-template-columns: 1fr;
        }

        .profile-achievements-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush