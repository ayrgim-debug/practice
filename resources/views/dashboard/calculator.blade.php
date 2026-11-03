@extends('layouts.app')

@section('title', 'Копилка')

@section('content')
    <!-- ===== ГЕРОЙ ===== -->
    <div class="hero">
        <span class="badge">🐖 Копи-Копи!</span>
        <h2>Сколько <span class="highlight">накопишь</span> за год?</h2>
        <p>Введи свою ежедневную трату — и мы покажем, сколько можно было бы отложить! 💰</p>
    </div>

    <!-- ===== СТАТИСТИКА (была на главной) ===== -->
    @if(isset($stats))
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number">{{ number_format($stats['total_savings'] ?? 0, 0, '', ' ') }}</div>
                <div class="stat-label">Накоплено за год</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-number">{{ $stats['habits_count'] ?? 0 }}</div>
                <div class="stat-label">Привычек</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📜</div>
                <div class="stat-number">{{ $stats['history_count'] ?? 0 }}</div>
                <div class="stat-label">Расчётов</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-number">{{ $stats['achievements_count'] ?? 0 }}</div>
                <div class="stat-label">Достижений</div>
            </div>
        </div>
    @endif

    <!-- ===== МЕЧТА (была на главной) ===== -->
    @if(isset($stats) && ($stats['has_dream'] ?? false))
        <div class="dream-card" style="margin-bottom: 24px; padding: 20px 24px; background: var(--bg-input); border-radius: var(--radius-sm); border: 2px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="font-size: 24px;">🎯</span>
                <h4 style="font-size: 17px; font-weight: 800; font-family: 'Fraunces', serif; color: var(--text-secondary); margin: 0;">Моя мечта</h4>
            </div>
            <div style="font-size: 22px; font-weight: 900; font-family: 'Fraunces', serif; color: #b8873b;">{{ $stats['dream_name'] }}</div>
            <div style="font-size: 15px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px;">{{ number_format($stats['dream_price'] ?? 0, 0, '', ' ') }} ₽</div>
            <div style="width: 100%; height: 12px; background: var(--border); border-radius: 60px; overflow: hidden;">
                <div style="width: {{ $stats['dream_progress'] ?? 0 }}%; height: 100%; background: var(--gradient-gold); border-radius: 60px; transition: width 1s ease;"></div>
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--text-muted); margin-top: 6px; text-align: right;">
                {{ $stats['dream_progress'] ?? 0 }}% от цели
            </div>
        </div>
    @endif

    <!-- ===== КАЛЬКУЛЯТОР ===== -->
    <div class="calc-grid">
        <div class="input-group">
            <label for="cost">💰 Сколько тратишь за раз?</label>
            <input type="number" id="cost" placeholder="Например, 150" value="0" min="0" step="1" />
        </div>
        <div class="input-group">
            <label for="frequency">📅 Как часто?</label>
            <select id="frequency">
                <option value="365">Каждый день</option>
                <option value="260" selected>По будням (5/7)</option>
                <option value="104">По выходным</option>
                <option value="52">Раз в неделю</option>
                <option value="12">Раз в месяц</option>
            </select>
        </div>
    </div>

    <div class="calc-actions">
        <button class="btn-primary" id="calcBtn">💰 Копи-Копи!</button>
        <button class="btn-secondary" id="resetBtn">🔄 Сброс</button>
        <button class="btn-secondary" onclick="shareResult()">📤 Поделиться</button>
    </div>

    <!-- ===== РЕЗУЛЬТАТ ===== -->
    <div class="result-box" id="resultBox">
        <div class="glow-text">✨ Ты можешь накопить ✨</div>
        <div class="result-row">
            <span class="result-label">💰 За год</span>
            <span class="result-number" id="resultAmount">0 <span>₽</span></span>
        </div>

        <!-- Свинья -->
        @include('partials.piggy')

        <!-- Мечта (в калькуляторе) -->
        <div class="dream-section">
            <h4>🎯 Моя мечта</h4>
            <div class="dream-inputs">
                <input type="text" id="dreamName" placeholder="Что хочешь купить?" value="{{ auth()->check() ? auth()->user()->dream_name : '' }}" />
                <input type="number" id="dreamPrice" placeholder="Сколько стоит? (₽)" value="{{ auth()->check() && auth()->user()->dream_price > 0 ? (float) auth()->user()->dream_price : '' }}" />
                <button class="btn-success" id="setDreamBtn">🎯 Установить</button>
            </div>
            <div class="dream-progress" id="dreamProgress">
                Прогресс к мечте: <span id="dreamPercent">0%</span>
            </div>
        </div>
    </div>

    <!-- ===== ДОЛГОСРОЧНЫЙ ПРОГНОЗ ===== -->
    <div class="long-term" id="longTerm">
        <div class="term-card">
            <div class="label">📆 2 года</div>
            <div class="value" id="year2">0 ₽</div>
        </div>
        <div class="term-card">
            <div class="label">📆 5 лет</div>
            <div class="value" id="year5">0 ₽</div>
        </div>
        <div class="term-card">
            <div class="label">📆 10 лет</div>
            <div class="value" id="year10">0 ₽</div>
        </div>
    </div>

    <!-- ===== ИНФЛЯЦИЯ ===== -->
    <div class="inflation-box">
        <div class="label">📉 Реальная стоимость через год (с учётом инфляции 9%)</div>
        <div class="value" id="inflationValue">0 ₽</div>
    </div>

    <!-- ===== ГРАФИК ===== -->
    <div class="chart-wrap">
        <h4>📈 Как растут твои копилки</h4>
        <canvas id="savingsChart"></canvas>
    </div>

    <!-- ===== ПОСЛЕДНИЕ РАСЧЁТЫ (были на главной) ===== -->
    @if(isset($recentHistory) && $recentHistory->count() > 0)
        <div class="section-title mt-20">
            <h3>📜 Последние расчёты</h3>
        </div>
        <div class="history-list-mini">
            @foreach($recentHistory as $item)
                <div class="history-item-mini" style="display: flex; justify-content: space-between; padding: 10px 16px; background: var(--bg-input); border-radius: var(--radius-sm); border: 2px solid var(--border); margin-bottom: 6px;">
                    <span style="color: var(--text-muted); font-size: 13px;">{{ $item->created_at->format('d.m.Y H:i') }}</span>
                    <span>{{ $item->cost }} ₽ × {{ $item->days }} дней</span>
                    <span style="font-weight: 800; color: #b8873b;">{{ number_format($item->total, 0, '', ' ') }} ₽</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ===== ПОЛЕЗНЫЕ СОВЕТЫ (были на главной) ===== -->
    @if(isset($tips) && $tips->count() > 0)
        <div class="section-title mt-20">
            <h3>💡 Полезные советы</h3>
        </div>
        <div class="tips-mini" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            @foreach($tips as $tip)
                <div style="background: var(--bg-input); border: 2px solid var(--border); border-radius: var(--radius-sm); padding: 14px 18px; transition: var(--transition);">
                    <span style="font-size: 24px;">{{ $tip->icon }}</span>
                    <div style="font-weight: 700; font-family: 'Fraunces', serif; color: var(--text-primary); margin-top: 4px;">{{ $tip->title }}</div>
                    <p style="font-size: 13px; color: var(--text-muted); font-weight: 600; margin-top: 2px;">{{ $tip->description }}</p>
                </div>
            @endforeach
        </div>
    @endif
@endsection