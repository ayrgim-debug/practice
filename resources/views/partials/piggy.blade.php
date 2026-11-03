<div class="piggy-section">
    <div class="piggy-emoji" id="piggyEmoji">🐖</div>
    <div class="piggy-bar-wrap">
        <div class="piggy-bar-bg">
            <div class="piggy-bar-fill" id="piggyFill" style="width: 0%;"></div>
        </div>
        <div class="piggy-label">
            <span>0 ₽</span>
            <span id="piggyTarget">{{ number_format(config('kopi.default_target', 100000), 0, '', ' ') }} ₽</span>
        </div>
    </div>
</div>

<div class="piggy-status">
    <span class="emoji-big">🐽</span> 
    <span id="piggyMessage">Начинай копить! 🐷</span>
</div>