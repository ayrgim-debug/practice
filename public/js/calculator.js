// ============================================================
// CALCULATOR.JS — ЛОГИКА КАЛЬКУЛЯТОРА
// Расчёт запускается ТОЛЬКО по кнопке «Копи-Копи!» (или Enter в поле суммы).
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
    const K = window.KOPI || {};
    const isAuth = !!K.isAuth;

    const costInput = document.getElementById('cost');
    const frequencySelect = document.getElementById('frequency');
    const calcBtn = document.getElementById('calcBtn');
    const resetBtn = document.getElementById('resetBtn');
    const resultAmount = document.getElementById('resultAmount');
    const piggyFill = document.getElementById('piggyFill');
    const piggyEmoji = document.getElementById('piggyEmoji');
    const piggyMessage = document.getElementById('piggyMessage');
    const dreamPercent = document.getElementById('dreamPercent');
    const dreamName = document.getElementById('dreamName');
    const dreamPrice = document.getElementById('dreamPrice');
    const setDreamBtn = document.getElementById('setDreamBtn');
    const year2 = document.getElementById('year2');
    const year5 = document.getElementById('year5');
    const year10 = document.getElementById('year10');
    const inflationValue = document.getElementById('inflationValue');

    // Страница с калькулятором? (на остальных страницах этот файл тоже подключён)
    const onCalculatorPage = !!(costInput && frequencySelect && calcBtn && resultAmount);

    let chartInstance = null;
    let lastYearly = 0;
    let busy = false;

    // ===== ОБЩИЕ ФУНКЦИИ (нужны футеру на всех страницах) =====
    window.shareResult = function () {
        const el = document.getElementById('resultAmount');
        const total = el ? el.textContent.trim() : '';
        const text = total && total !== '0 ₽'
            ? `🐷 Я могу накопить ${total} за год с Копи-Копилкой! Попробуй и ты! 💰`
            : '🐷 Копи-Копилка — посчитай, сколько можно накопить за год! 💰';

        if (navigator.share) {
            navigator.share({ title: 'Копи-Копилка', text: text, url: window.location.href }).catch(() => {});
        } else if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                alert('📋 Результат скопирован! Поделись с друзьями! 🐷');
            }).catch(() => { prompt('Скопируй текст:', text); });
        } else {
            prompt('Скопируй текст:', text);
        }
    };

    window.resetAll = function () {
        if (!confirm('🐷 Сбросить всё?')) return;
        if (onCalculatorPage) {
            costInput.value = '0';
            frequencySelect.value = '260';
            resetView();
        } else {
            window.location.href = '/';
        }
    };

    window.exportPDF = function () {
        const element = document.getElementById('app');
        if (typeof html2pdf === 'undefined' || !element) {
            alert('Экспорт PDF сейчас недоступен.');
            return;
        }
        const originalOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        html2pdf()
            .set({
                margin: [10, 10, 10, 10],
                filename: 'Копи-Копилка_отчет.pdf',
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            })
            .from(element)
            .save()
            .then(() => { document.body.style.overflow = originalOverflow; })
            .catch((err) => {
                document.body.style.overflow = originalOverflow;
                console.error('PDF ошибка:', err);
                alert('Ошибка экспорта. Попробуй позже.');
            });
    };

    if (!onCalculatorPage) return;

    // ===== СБРОС ОТОБРАЖЕНИЯ =====
    function resetView() {
        lastYearly = 0;
        resultAmount.innerHTML = '0 <span>₽</span>';
        piggyFill.style.width = '0%';
        piggyEmoji.textContent = '🐖';
        piggyMessage.textContent = '🐷 Введи сумму и нажми «Копи-Копи!»';
        year2.textContent = '0 ₽';
        year5.textContent = '0 ₽';
        year10.textContent = '0 ₽';
        inflationValue.textContent = '0 ₽';
        updateDream(0);
        updateChart(Array(12).fill(0));
    }

    // ===== РАСЧЁТ (только по нажатию кнопки) =====
    function calculate() {
        if (busy) return;

        const cost = parseFloat(costInput.value) || 0;
        const days = parseInt(frequencySelect.value) || 0;

        if (cost <= 0) {
            resetView();
            return;
        }

        busy = true;
        fetch(K.routes.calculate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': K.csrf
            },
            body: JSON.stringify({ cost, frequency: days })
        })
        .then(response => response.json())
        .then(data => {
            if (typeof data.yearly === 'undefined') {
                throw new Error('Некорректный ответ сервера');
            }

            lastYearly = data.yearly;
            animateNumber(resultAmount, data.yearly);
            piggyFill.style.width = data.percent + '%';

            const remaining = data.target - data.yearly;

            if (data.percent >= 100) {
                piggyEmoji.textContent = '🐷';
                piggyMessage.textContent = '🎉 Ура! Цель достигнута! Ты молодец! 🎉';
                triggerCelebration();
            } else if (data.percent >= 70) {
                piggyEmoji.textContent = '🐽';
                piggyMessage.textContent = '🔥 Почти у цели! Осталось ' + remaining.toLocaleString() + ' ₽!';
            } else if (data.percent >= 30) {
                piggyEmoji.textContent = '🐖';
                piggyMessage.textContent = '💪 Хороший темп! Осталось ' + remaining.toLocaleString() + ' ₽ до цели.';
            } else if (data.percent > 0) {
                piggyEmoji.textContent = '🐖';
                piggyMessage.textContent = '🐷 Начинай копить! Осталось ' + remaining.toLocaleString() + ' ₽ до цели.';
            } else {
                piggyEmoji.textContent = '🐖';
                piggyMessage.textContent = '🐷 Начинай копить уже сегодня!';
            }

            piggyEmoji.style.transform = 'scale(1.2) rotate(-5deg)';
            setTimeout(() => { piggyEmoji.style.transform = 'scale(1) rotate(0deg)'; }, 400);

            year2.textContent = data.two_years.toLocaleString() + ' ₽';
            year5.textContent = data.five_years.toLocaleString() + ' ₽';
            year10.textContent = data.ten_years.toLocaleString() + ' ₽';
            inflationValue.textContent = data.real_value.toLocaleString() + ' ₽';
            updateDream(data.yearly);
            updateChart(data.monthly);
        })
        .catch(error => {
            console.error('Ошибка расчёта:', error);
            alert('Не удалось выполнить расчёт. Обнови страницу и попробуй ещё раз.');
        })
        .finally(() => { busy = false; });
    }

    // ===== АНИМАЦИЯ ЧИСЛА =====
    function animateNumber(element, target, duration = 800) {
        const start = parseFloat(element.textContent.replace(/[^0-9.]/g, '')) || 0;
        const diff = target - start;
        if (diff === 0 || target === 0) {
            element.innerHTML = target.toLocaleString() + ' <span>₽</span>';
            return;
        }
        const steps = 40;
        const step = diff / steps;
        let current = start;
        let count = 0;
        const timer = setInterval(() => {
            count++;
            current += step;
            if (count >= steps) {
                current = target;
                clearInterval(timer);
            }
            element.innerHTML = Math.round(current).toLocaleString() + ' <span>₽</span>';
        }, duration / steps);
    }

    // ===== МЕЧТА =====
    function updateDream(yearly) {
        if (!dreamPercent || !dreamPrice) return;
        const price = parseFloat(dreamPrice.value);
        if (isNaN(price) || price <= 0) {
            dreamPercent.textContent = '0%';
            return;
        }
        const percent = Math.min((yearly / price) * 100, 100);
        dreamPercent.textContent = percent >= 100 ? '🎉 Достигнута! 🎉' : percent.toFixed(1) + '%';
    }

    // ===== ЗАГРУЗКА МЕЧТЫ (только для вошедших пользователей) =====
    function loadDream() {
        if (!isAuth || !K.routes.dreamGet || !dreamName || !dreamPrice) return;
        fetch(K.routes.dreamGet, { headers: { 'Accept': 'application/json' } })
            .then(response => response.ok ? response.json() : null)
            .then(data => {
                if (data && data.has_dream) {
                    dreamName.value = data.dream_name;
                    dreamPrice.value = parseFloat(data.dream_price);
                }
            })
            .catch(() => {});
    }

    // ===== ГРАФИК =====
    function chartColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            text: isDark ? '#fed7aa' : '#5c4033',
            grid: isDark ? '#4d3728' : '#fde4c8'
        };
    }

    function updateChart(monthlyData) {
        const ctx = document.getElementById('savingsChart');
        if (!ctx || typeof Chart === 'undefined') return;

        const c = chartColors();
        if (chartInstance) chartInstance.destroy();

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                datasets: [{
                    label: 'Накопления',
                    data: monthlyData || Array(12).fill(0),
                    borderColor: '#fb923c',
                    backgroundColor: 'rgba(251, 146, 60, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#fb923c',
                    pointRadius: 5,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 11 } } },
                    y: {
                        grid: { color: c.grid },
                        ticks: {
                            color: c.text,
                            font: { size: 11 },
                            callback: function (value) { return value.toLocaleString() + ' ₽'; }
                        }
                    }
                }
            }
        });
    }

    window.updateChartTheme = function () {
        if (!chartInstance) return;
        const c = chartColors();
        chartInstance.options.scales.x.ticks.color = c.text;
        chartInstance.options.scales.x.grid.color = c.grid;
        chartInstance.options.scales.y.ticks.color = c.text;
        chartInstance.options.scales.y.grid.color = c.grid;
        chartInstance.update();
    };

    // ===== КОНФЕТТИ =====
    function triggerCelebration() {
        const emojis = ['🎉', '✨', '🌟', '🎊', '💫', '🐷', '💰'];
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('span');
            el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
            el.style.cssText = `
                position: fixed;
                left: ${Math.random() * 100}%;
                top: -20px;
                font-size: ${20 + Math.random() * 30}px;
                pointer-events: none;
                z-index: 9999;
                transition: all ${1 + Math.random() * 2}s ease-in;
                opacity: 1;
            `;
            document.body.appendChild(el);

            setTimeout(() => {
                el.style.top = (80 + Math.random() * 20) + '%';
                el.style.left = (Math.random() * 100) + '%';
                el.style.opacity = '0';
                el.style.transform = 'rotate(' + (Math.random() * 720) + 'deg)';
            }, 50);

            setTimeout(() => el.remove(), 3500);
        }
    }

    // ===== СОХРАНЕНИЕ МЕЧТЫ =====
    if (setDreamBtn) {
        setDreamBtn.addEventListener('click', function () {
            if (!isAuth) {
                alert('🐷 Чтобы сохранить мечту, войди в аккаунт.');
                window.location.href = '/login';
                return;
            }

            const name = dreamName.value.trim();
            const price = parseFloat(dreamPrice.value);

            if (!name || !price || price <= 0) {
                alert('🐷 Введи название и стоимость своей мечты!');
                return;
            }

            fetch(K.routes.dreamUpdate, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': K.csrf
                },
                body: JSON.stringify({ dream_name: name, dream_price: price })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('🎯 Мечта "' + data.dream_name + '" установлена! Копи дальше! 🐷');
                    updateDream(lastYearly); // без нового расчёта и без записи в историю
                }
            })
            .catch(error => {
                console.error('Ошибка сохранения мечты:', error);
            });
        });
    }

    // ===== КНОПКИ (авторасчёта при вводе больше нет) =====
    calcBtn.addEventListener('click', calculate);

    costInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            calculate();
        }
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            costInput.value = '0';
            frequencySelect.value = '260';
            resetView();
        });
    }

    // ===== ИНИЦИАЛИЗАЦИЯ (без расчёта) =====
    updateChart(Array(12).fill(0));
    loadDream();
});
