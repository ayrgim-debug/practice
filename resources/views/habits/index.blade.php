@extends('layouts.app')

@section('title', 'Привычки')

@section('content')
    <div class="hero">
        <h2>📋 Мои денежные привычки</h2>
        <p>Добавь все свои ежедневные траты — и мы посчитаем, сколько можно сэкономить! 💰</p>
    </div>

    <div class="habits-section">
        <div class="habit-input">
            <input type="text" id="habitName" placeholder="Что тратишь? (кофе, такси...)" />
            <input type="number" id="habitCost" placeholder="Сколько? (₽)" />
            <select id="habitFreq">
                <option value="365">Каждый день</option>
                <option value="260" selected>По будням</option>
                <option value="104">По выходным</option>
                <option value="52">Раз в неделю</option>
                <option value="12">Раз в месяц</option>
            </select>
            <button class="btn-success" id="addHabitBtn">➕ Добавить</button>
        </div>

        @if($habits->count() > 0)
            <div class="calc-actions" style="margin-bottom: 12px;">
                <button class="btn-secondary" id="clearHabitsBtn">🗑️ Очистить все</button>
            </div>
        @endif

        <div class="habits-table-wrap">
            <table class="habits-table">
                <thead>
                    <tr>
                        <th>Привычка</th>
                        <th>Цена (₽)</th>
                        <th>Частота</th>
                        <th>В год (₽)</th>
                        <th style="width:50px;">🗑️</th>
                    </tr>
                </thead>
                <tbody id="habitsBody">
                    @forelse($habits as $habit)
                        <tr data-id="{{ $habit->id }}">
                            <td>{{ $habit->name }}</td>
                            <td>{{ number_format($habit->cost, 2) }} ₽</td>
                            <td>{{ $habit->getFrequencyLabel() }}</td>
                            <td>{{ number_format($habit->getYearlyTotal(), 0, '', ' ') }} ₽</td>
                            <td>
                                <button class="btn-danger delete-habit" data-id="{{ $habit->id }}">✕</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-habits">🐷 Пока нет привычек. Добавь первую!</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3"><strong>💰 Итого за год</strong></td>
                        <td><strong id="habitsTotal">{{ number_format($total, 0, '', ' ') }} ₽</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="inflation-box mt-16">
        <div class="label">💡 Экономия на привычках за 5 лет</div>
        <div class="value" id="habitsFiveYear">{{ number_format($total * 5, 0, '', ' ') }} ₽</div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Добавление привычки
        document.getElementById('addHabitBtn').addEventListener('click', function() {
            const name = document.getElementById('habitName').value.trim();
            const cost = parseFloat(document.getElementById('habitCost').value);
            const frequency = parseInt(document.getElementById('habitFreq').value);

            if (!name || isNaN(cost) || cost <= 0) {
                alert('🐷 Введи название и цену привычки!');
                return;
            }

            fetch('{{ route("habits.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, cost, frequency })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });

        const delHeaders = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };

        function sendDelete(url) {
            return fetch(url, { method: 'DELETE', headers: delHeaders })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { location.reload(); }
                    else { alert('Не удалось удалить. Попробуй ещё раз.'); }
                })
                .catch(() => alert('Ошибка соединения. Попробуй ещё раз.'));
        }

        // Удаление одной привычки
        document.querySelectorAll('.delete-habit').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Удалить привычку?')) return;
                sendDelete(`/habits/${this.dataset.id}`);
            });
        });

        // Очистка всех привычек
        const clearBtn = document.getElementById('clearHabitsBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (!confirm('Удалить ВСЕ привычки?')) return;
                sendDelete('{{ route("habits.clear") }}');
            });
        }
    });
</script>
@endpush