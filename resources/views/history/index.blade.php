@extends('layouts.app')

@section('title', 'История')

@section('content')
    <div class="hero">
        <h2>📜 История твоих копилок</h2>
        <p>Все расчёты в одном месте. Смотри, как растут твои сбережения! 📈</p>
    </div>

    <div class="calc-actions">
        <button class="btn-secondary" id="clearHistoryBtn">🗑️ Очистить</button>
        <button class="btn-secondary" onclick="exportPDF()">📄 Скачать PDF</button>
    </div>

    <div class="history-list" id="historyList">
        @forelse($histories as $item)
            <div class="history-item">
                <span class="date">{{ $item->created_at->format('d.m.Y H:i') }}</span>
                <span>{{ $item->cost }} ₽ × {{ $item->days }} дней</span>
                <span class="amount">{{ number_format($item->total, 0, '', ' ') }} ₽</span>
                <button class="btn-danger delete-history" data-id="{{ $item->id }}">✕</button>
            </div>
        @empty
            <div class="empty-habits">🐷 История пуста. Сделай первый расчёт!</div>
        @endforelse
    </div>

    {{ $histories->links() }}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };

        function send(url) {
            return fetch(url, { method: 'DELETE', headers: headers })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { location.reload(); }
                    else { alert('Не удалось удалить. Попробуй ещё раз.'); }
                })
                .catch(() => alert('Ошибка соединения. Попробуй ещё раз.'));
        }

        // Удаление одной записи
        document.querySelectorAll('.delete-history').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Удалить запись из истории?')) return;
                send(`/history/${this.dataset.id}`);
            });
        });

        // Очистка всей истории
        const clearBtn = document.getElementById('clearHistoryBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (!confirm('Очистить всю историю расчётов?')) return;
                send('{{ route("history.clear") }}');
            });
        }
    });
</script>
@endpush