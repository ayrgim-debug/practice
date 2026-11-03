// ============================================================
// APP.JS — ОСНОВНОЙ СКРИПТ
// ============================================================

document.addEventListener('DOMContentLoaded', function() {

    // ===== ТЁМНАЯ ТЕМА =====
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        function applyTheme(theme) {
            const html = document.documentElement;
            if (theme === 'dark') {
                html.setAttribute('data-theme', 'dark');
                themeToggle.textContent = '☀️ Светлая';
                document.cookie = 'theme=dark; path=/; max-age=31536000';
            } else {
                html.removeAttribute('data-theme');
                themeToggle.textContent = '🌙 Тёмная';
                document.cookie = 'theme=light; path=/; max-age=31536000';
            }
            
            if (typeof window.updateChartTheme === 'function') {
                window.updateChartTheme();
            }
        }

        function loadTheme() {
            const cookies = document.cookie.split(';');
            let savedTheme = null;
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'theme') {
                    savedTheme = value;
                    break;
                }
            }
            
            if (!savedTheme) {
                savedTheme = localStorage.getItem('kopi-theme');
            }
            
            applyTheme(savedTheme || 'light');
        }

        loadTheme();

        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const html = document.documentElement;
            const isDark = html.getAttribute('data-theme') === 'dark';
            const newTheme = isDark ? 'light' : 'dark';
            
            applyTheme(newTheme);
            localStorage.setItem('kopi-theme', newTheme);
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

    // ===== АКТИВНАЯ ВКЛАДКА =====
    const currentPath = window.location.pathname;
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => {
        const tab = btn.dataset.tab;
        if (tab === 'calculator' && (currentPath === '/' || currentPath === '/calculator' || currentPath === '/dashboard')) {
            btn.classList.add('active');
        } else if (tab === 'habits' && currentPath.includes('/habits')) {
            btn.classList.add('active');
        } else if (tab === 'history' && currentPath.includes('/history')) {
            btn.classList.add('active');
        } else if (tab === 'tips' && currentPath.includes('/tips')) {
            btn.classList.add('active');
        } else if (tab === 'account' && currentPath.includes('/profile')) {
            btn.classList.add('active');
        } else if (tab === 'calculator' && currentPath === '/') {
            btn.classList.add('active');
        }
    });

    console.log('🐷 Копи-Копилка загружена!');
});