<nav class="tabs" id="tabNav">
    <button class="tab-btn {{ request()->routeIs('calculator') ? 'active' : '' }}" data-tab="calculator" onclick="window.location.href='{{ route('calculator') }}'">
        💰 Копилка
    </button>
    <button class="tab-btn {{ request()->routeIs('habits*') ? 'active' : '' }}" data-tab="habits" onclick="window.location.href='{{ route('habits.index') }}'">
        📋 Привычки
    </button>
    <button class="tab-btn {{ request()->routeIs('history*') ? 'active' : '' }}" data-tab="history" onclick="window.location.href='{{ route('history') }}'">
        📜 История
    </button>
    <button class="tab-btn {{ request()->routeIs('tips*') ? 'active' : '' }}" data-tab="tips" onclick="window.location.href='{{ route('tips') }}'">
        💡 Идеи
    </button>
    <button class="tab-btn {{ request()->routeIs('profile') ? 'active' : '' }}" data-tab="profile" onclick="window.location.href='{{ route('profile') }}'">
        👤 Профиль
    </button>
    <button class="tab-btn {{ request()->routeIs('support*') ? 'active' : '' }}" data-tab="support" onclick="window.location.href='{{ route('support') }}'">
        🆘 Поддержка
    </button>
    @auth
        @if(auth()->user()->isAdmin())
            @php $newTickets = \App\Models\SupportTicket::where('status', 'new')->count(); @endphp
            <button class="tab-btn {{ request()->routeIs('admin.*') ? 'active' : '' }}" data-tab="admin" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                ⚙️ Админка
                @if($newTickets > 0)
                    <span style="display:inline-block; min-width:18px; padding:0 5px; margin-left:4px; border-radius:60px; background:#b33b2c; color:#fff; font-size:11px; line-height:18px; text-align:center;">{{ $newTickets }}</span>
                @endif
            </button>
        @endif
    @endauth
</nav>