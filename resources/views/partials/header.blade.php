<header class="header">
    <a class="logo" href="{{ route('calculator') }}">
        <span class="logo-icon">@include('partials.logo-mark')</span>
        <div>
            <h1>Копи-Копилка</h1>
            <span class="sub">✨ Копи и копи! ✨</span>
        </div>
    </a>
    <div class="header-actions">
        <span class="user-counter">
            <span class="heart">❤️</span> <span id="userCount">{{ number_format(rand(2800, 3500)) }}</span> копят с нами
        </span>
        
        @auth
            <span class="user-badge" id="userBadge">
                <span class="avatar">{{ auth()->user()->name[0] ?? 'К' }}</span>
                <span id="userNameDisplay">{{ auth()->user()->name }}</span>
            </span>
            <a href="{{ route('profile') }}" style="text-decoration: none; color: var(--text-secondary); font-weight: 700; font-size: 13px; padding: 6px 16px; border: 2px solid var(--border); border-radius: 60px; transition: var(--transition);">
                👤 Профиль
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #b33b2c; font-weight: 700; font-size: 13px; cursor: pointer; padding: 6px 16px; border: 2px solid rgba(179, 59, 44, 0.35); border-radius: 60px; transition: var(--transition); font-family: 'Fraunces', serif;">
                    🚪 Выйти
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" style="text-decoration: none; color: var(--text-secondary); font-weight: 700; font-size: 13px; padding: 6px 16px; border: 2px solid var(--border); border-radius: 60px; transition: var(--transition);">
                🔑 Войти
            </a>
            <a href="{{ route('register') }}" style="text-decoration: none; background: var(--gradient-main); color: white; font-weight: 700; font-size: 13px; padding: 6px 16px; border: none; border-radius: 60px; transition: var(--transition);">
                📝 Регистрация
            </a>
        @endauth
        
        <button class="theme-toggle" id="themeToggle">
            @if(session('theme', 'light') === 'dark')
                ☀️ Светлая
            @else
                🌙 Тёмная
            @endif
        </button>
    </div>
</header>