// ===== ТЕМА =====
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;

    // Функция для применения темы
    function applyTheme(theme) {
        const html = document.documentElement;
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            if (themeToggle) themeToggle.textContent = '☀️ Светлая';
            document.cookie = 'theme=dark; path=/; max-age=31536000';
        } else {
            html.removeAttribute('data-theme');
            if (themeToggle) themeToggle.textContent = '🌙 Тёмная';
            document.cookie = 'theme=light; path=/; max-age=31536000';
        }
        
        // Обновляем график, если он есть
        if (window.updateChartTheme) {
            window.updateChartTheme();
        }
    }

    // Загрузка сохранённой темы
    function loadTheme() {
        // Проверяем cookie
        const cookies = document.cookie.split(';');
        let savedTheme = null;
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'theme') {
                savedTheme = value;
                break;
            }
        }
        
        // Если тема сохранена в cookie — применяем
        if (savedTheme) {
            applyTheme(savedTheme);
        } else {
            // Проверяем localStorage (для обратной совместимости)
            const localTheme = localStorage.getItem('kopi-theme');
            if (localTheme) {
                applyTheme(localTheme);
            } else {
                // По умолчанию — светлая
                applyTheme('light');
            }
        }
    }

    // Применяем тему при загрузке
    loadTheme();

    // Обработчик клика по кнопке
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            const isDark = html.getAttribute('data-theme') === 'dark';
            applyTheme(isDark ? 'light' : 'dark');
            
            // Сохраняем в localStorage для обратной совместимости
            localStorage.setItem('kopi-theme', isDark ? 'light' : 'dark');
        });
    }

    // ===== FLASH СООБЩЕНИЯ =====
    const flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (flash.parentNode) {
                    flash.remove();
                }
            }, 500);
        }, 4000);
    }

    console.log('🌓 Тёмная тема загружена!');
});