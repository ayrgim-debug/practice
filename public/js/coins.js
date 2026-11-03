// ============================================================
// COINS.JS — ФОН С МОНЕТКАМИ
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    const bg = document.getElementById('coinsBg');
    if (!bg) return;

    const emojis = ['💵', '💰', '✨', '🐷', '🌟', '💎'];
    
    for (let i = 0; i < 25; i++) {
        const el = document.createElement('span');
        el.className = 'coin-particle';
        el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        el.style.left = Math.random() * 100 + '%';
        el.style.fontSize = (20 + Math.random() * 30) + 'px';
        el.style.animationDuration = (15 + Math.random() * 25) + 's';
        el.style.animationDelay = (Math.random() * 20) + 's';
        bg.appendChild(el);
    }
});