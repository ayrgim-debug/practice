@extends('layouts.app')

@section('title', 'Идеи')

@section('content')
    <div class="hero">
        <h2>💡 Идеи для копилки</h2>
        <p>Простые лайфхаки, которые помогут тебе копить быстрее и веселее! 🚀</p>
    </div>

    <div class="tips-grid">
        @foreach($tips as $tip)
            <div class="tip-card">
                <span class="tip-icon">{{ $tip->icon }}</span>
                <h5>{{ $tip->title }}</h5>
                <p>{{ $tip->description }}</p>
                <button class="btn-save-tip {{ in_array($tip->id, $savedTipIds) ? 'saved' : '' }}" 
                        data-id="{{ $tip->id }}">
                    {{ in_array($tip->id, $savedTipIds) ? '⭐ Сохранён' : '💾 Сохранить' }}
                </button>
            </div>
        @endforeach
    </div>

    <div class="inflation-box mt-20" style="border-color: #b8873b;">
        <div class="label">🌟 Главный секрет копилки</div>
        <div class="value" style="font-size:18px; font-weight:700; color:var(--text-primary);">
            "Начни с 2-3 привычек, которые легче всего заменить, и копи с радостью!" 🐷
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-save-tip').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/tips/${id}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.saved) {
                            this.textContent = '⭐ Сохранён';
                            this.classList.add('saved');
                        } else {
                            this.textContent = '💾 Сохранить';
                            this.classList.remove('saved');
                        }
                    }
                });
            });
        });
    });
</script>
@endpush