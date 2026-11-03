document.addEventListener('DOMContentLoaded', function() {
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

    let chartInstance = null;

    // ===== РАСЧЁТ =====
    function calculate() {
        const cost = parseFloat(costInput.value) || 0;
        const days = parseInt(frequencySelect.value) || 0;

        // Если cost = 0, показываем 0
        if (cost <= 0) {
            resultAmount.innerHTML = '0 <span>₽</span>';
            piggyFill.style.width = '0%';
            piggyEmoji.textContent = '🐖';
            piggyMessage.textContent = '🐷 Введи сумму и нажми "Копи-Копи!"';
            year2.textContent = '0 ₽';
            year5.textContent = '0 ₽';
            year10.textContent = '0 ₽';
            inflationValue.textContent = '0 ₽';
            updateDream(0);
            return;
        }

        fetch(window.KOPI.routes.calculate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.KOPI.csrf
            },
            body: JSON.stringify({ cost, frequency: days })
        })
        .then(response => response.json())
        .then(data => {
            // Обновляем результат
            animateNumber(resultAmount, data.yearly);

            // Обновляем копилку
            piggyFill.style.width = data.percent + '%';

            // Свинья меняет эмоции
            if (data.percent >= 100) {
                piggyEmoji.textContent = '🐷';
                piggyMessage.textContent = '🎉 Ура! Цель достигнута! Ты молодец! 🎉';
                triggerCelebration();
            } else if (data.percent >= 70) {
                piggyEmoji.textContent = '🐽';
                piggyMessage.textContent = '🔥 Почти у цели! Осталось ' + (data.target - data.yearly).toLocaleString() + ' ₽!';
            } else if (data.percent >= 30) {
                piggyEmoji.textContent = '🐖';
                piggyMessage.textContent = '💪 Хороший темп! Осталось ' + (data.target - data.yearly).toLocaleString() + ' ₽ до цели.';
            } else {
                piggyEmoji.textContent = '🐖';
                piggyMessage.textContent = '🐷 Начинай копить! Осталось ' + (data.target - data.yearly).toLocaleString() + ' ₽ до цели.';
            }

            piggyEmoji.style.transform = 'scale(1.2) rotate(-5deg)';
            setTimeout(() => {
                piggyEmoji.style.transform = 'scale(1) rotate(0deg)';
            }, 400);

            // Долгосрочный прогноз
            year2.textContent = data.two_years.toLocaleString() + ' ₽';
            year5.textContent = data.five_years.toLocaleString() + ' ₽';
            year10.textContent = data.ten_years.toLocaleString() + ' ₽';

            // Инфляция
            inflationValue.textContent = data.real_value.toLocaleString() + ' ₽';

            // Мечта
            updateDream(data.yearly);

            // График
            updateChart(data.monthly);
        });
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
        const price = parseFloat(dreamPrice.value);
        if (isNaN(price) || price <= 0) {
            dreamPercent.textContent = '0%';
            return;
        }
        const percent = Math.min((yearly / price) * 100, 100);
        dreamPercent.textContent = percent.toFixed(1) + '%';
        if (percent >= 100) {
            dreamPercent.textContent = '🎉 Достигнута! 🎉';
        }
    }

    // ===== ЗАГРУЗКА МЕЧТЫ ИЗ ПРОФИЛЯ =====
    function loadDream() {
        fetch(window.KOPI.routes.dreamGet)
            .then(response => response.json())
            .then(data => {
                if (data.has_dream) {
                    dreamName.value = data.dream_name;
                    dreamPrice.value = data.dream_price;
                    // Пересчитываем с учётом мечты
                    setTimeout(calculate, 100);
                }
            })
            .catch(() => {
                // Если ошибка — игнорируем
            });
    }

    // ===== ГРАФИК =====
    function updateChart(monthlyData) {
        const ctx = document.getElementById('savingsChart');
        if (!ctx) return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#cbd5e1' : '#5c4033';
        const gridColor = isDark ? '#2d2d4a' : '#fde4c8';

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                datasets: [{
                    label: 'Накопления',
                    data: monthlyData,
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 11, family: 'Comfortaa' } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { size: 11, family: 'Comfortaa' },
                            callback: function(value) {
                                return value.toLocaleString() + ' ₽';
                            }
                        }
                    }
                }
            }
        });
    }

    // ===== КОНФЕТТИ =====
    function triggerCelebration() {
        const emojis = ['🎉', '✨', '🌟', '🎊', '💫', '🐷', '🪙'];
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

            setTimeout(() => el.remove(), 2500);
        }
    }

    // ===== СОХРАНЕНИЕ МЕЧТЫ =====
    setDreamBtn.addEventListener('click', function() {
        if (!dreamName.value.trim() || !dreamPrice.value) {
            alert('🐷 Введи название и стоимость своей мечты!');
            return;
        }

        fetch(window.KOPI.routes.dreamUpdate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.KOPI.csrf
            },
            body: JSON.stringify({
                dream_name: dreamName.value.trim(),
                dream_price: parseFloat(dreamPrice.value)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('🎯 Мечта "' + data.dream_name + '" установлена! Копи дальше! 🐷');
                calculate();
            }
        });
    });

    // ===== АВТОРАСЧЁТ =====
    costInput.addEventListener('input', calculate);
    frequencySelect.addEventListener('change', calculate);
    calcBtn.addEventListener('click', calculate);
    
    resetBtn.addEventListener('click', function() {
        costInput.value = '0';
        frequencySelect.value = '260';
        calculate();
    });

    // ===== ПОДГРУЗКА ПРИ ЗАПУСКЕ =====
    // Сначала загружаем мечту, потом делаем расчёт
    loadDream();
    
    // Запускаем расчёт с задержкой (ждём загрузку мечты)
    setTimeout(calculate, 300);

    // ===== ГЛОБАЛЬНЫЕ ФУНКЦИИ =====
    window.shareResult = function() {
        const total = document.getElementById('resultAmount').textContent.trim();
        const text = `🐷 Я могу накопить ${total} за год с Копи-Копилкой! Попробуй и ты! 🪙`;
        if (navigator.share) {
            navigator.share({ title: 'Копи-Копилка', text: text, url: window.location.href })
                .catch(() => {});
        } else {
            navigator.clipboard.writeText(text).then(() => {
                alert('📋 Результат скопирован! Поделись с друзьями! 🐷');
            }).catch(() => {
                prompt('Скопируй текст:', text);
            });
        }
    };

    window.resetAll = function() {
        if (!confirm('🐷 Сбросить всё?')) return;
        costInput.value = '0';
        frequencySelect.value = '260';
        calculate();
    };

    window.exportPDF = function() {
        const element = document.getElementById('app');
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
            .then(() => {
                document.body.style.overflow = originalOverflow;
            })
            .catch(() => {
                document.body.style.overflow = originalOverflow;
                alert('Ошибка экспорта. Попробуй позже.');
            });
    };
    // ===== ОБНОВЛЕНИЕ ГРАФИКА ПРИ СМЕНЕ ТЕМЫ =====
window.updateChartTheme = function() {
    if (chartInstance) {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#cbd5e1' : '#5c4033';
        const gridColor = isDark ? '#2d2d4a' : '#fde4c8';
        
        chartInstance.options.scales.x.ticks.color = textColor;
        chartInstance.options.scales.x.grid.color = gridColor;
        chartInstance.options.scales.y.ticks.color = textColor;
        chartInstance.options.scales.y.grid.color = gridColor;
        chartInstance.update();
    }
};
    console.log('🐷 Копи-Копилка загружена! Копи с радостью! 💰');
});