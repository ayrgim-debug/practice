@extends('layouts.app')

@section('title', 'Идеи')

@section('content')
    <div class="hero">
        <h2>💡 Идеи для копилки</h2>
        <p>Простые лайфхаки, которые помогут тебе копить быстрее и веселее! 🚀</p>
    </div>

    <div class="tips-grid">
        <!-- ===== ОСНОВНЫЕ СОВЕТЫ (из базы) ===== -->
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

        <!-- ===== НОВЫЕ ИДЕИ (добавлены вручную) ===== -->
        <div class="tip-card">
            <span class="tip-icon">📱</span>
            <h5>Приложение для учёта</h5>
            <p>Установи приложение для учёта расходов — видишь каждую копейку и легче планировать бюджет.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🛒</span>
            <h5>Покупай оптом</h5>
            <p>Крупы, макароны, бытовая химия оптом экономят до 30% бюджета в месяц.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🚲</span>
            <h5>Велосипед вместо авто</h5>
            <p>Пересядь на велосипед в тёплое время — экономишь на бензине и парковке.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🔋</span>
            <h5>Заряжай с умом</h5>
            <p>Заряжай телефон ночью по дешёвому тарифу — мелочь, а приятно.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">📚</span>
            <h5>Библиотека вместо покупки</h5>
            <p>Книги, фильмы, игры бери в библиотеке или пользуйся подписками — дешевле, чем покупать.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🧺</span>
            <h5>Стирай при полной загрузке</h5>
            <p>Стиральная машина при полной загрузке экономит до 30% электроэнергии и воды.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🌱</span>
            <h5>Выращивай зелень дома</h5>
            <p>Зелень на подоконнике — свежо, полезно и экономит до 2000 ₽ в месяц.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🏦</span>
            <h5>Накопительный счёт</h5>
            <p>Открой накопительный счёт с процентами — деньги будут работать на тебя.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🎯</span>
            <h5>Правило 30 дней</h5>
            <p>Хочешь что-то купить? Подожди 30 дней. Если всё ещё хочешь — покупай.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">📅</span>
            <h5>Бюджет на неделю</h5>
            <p>Планируй бюджет на неделю, а не на месяц — так проще контролировать.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🚶</span>
            <h5>Ходи пешком</h5>
            <p>Если до работы меньше 5 км — ходи пешком. Экономишь на транспорте и укрепляешь здоровье.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🎮</span>
            <h5>Игры замени хобби</h5>
            <p>Вместо покупки новых игр найди бесплатные альтернативы или вспомни старые.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🍳</span>
            <h5>Готовь дома</h5>
            <p>Готовь завтрак и обед дома — это в 3-4 раза дешевле, чем покупать готовую еду.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">☕</span>
            <h5>Кофе с собой</h5>
            <p>Заваривай кофе в термокружку — экономишь до 50 000 ₽ в год на кофе навынос.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">💡</span>
            <h5>Светодиодные лампы</h5>
            <p>Замени все лампы на светодиодные — они служат дольше и тратят на 80% меньше энергии.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🚿</span>
            <h5>Душ вместо ванны</h5>
            <p>Принимай душ вместо ванны — экономишь до 20 000 ₽ в год на воде.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">📦</span>
            <h5>Не покупай лишнее</h5>
            <p>Перед походом в магазин составляй список и строго следуй ему.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">💳</span>
            <h5>Кэшбэк везде</h5>
            <p>Используй карты с кэшбэком на все покупки — возвращай до 5% от каждой траты.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">📊</span>
            <h5>Анализируй траты</h5>
            <p>Раз в неделю просматривай свои расходы — найди, где можно сократить.</p>
        </div>

        <div class="tip-card">
            <span class="tip-icon">🎁</span>
            <h5>Дари эмоции</h5>
            <p>Вместо дорогих подарков дари впечатления — они запоминаются надолго и часто дешевле.</p>
        </div>
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