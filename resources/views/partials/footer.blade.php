<div class="footnote">
    <span>🐷 <strong>Копи-Копилка</strong> — копи с радостью! ✨</span>
    <div class="footnote-actions">
        <button onclick="window.location.href='{{ route('support', ['from' => url()->current()]) }}'">🆘 Сообщить об ошибке</button>
        <button onclick="shareResult()">📤 Поделиться</button>
        <button onclick="resetAll()">🔄 Сбросить всё</button>
        <button onclick="exportPDF()">📄 PDF</button>
    </div>
</div>