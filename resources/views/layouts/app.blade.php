<!DOCTYPE html>
<html lang="ru" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Копи-Копилка — @yield('title', 'Копи и копи!')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/kopi.css') }}">
    
    @stack('styles')
</head>
<body>

    <!-- ===== ФОН С МОНЕТКАМИ ===== -->
    <div class="coins-bg" id="coinsBg"></div>

    <!-- ===== ОСНОВНОЙ КОНТЕЙНЕР ===== -->
    <div class="app" id="app">
        
        <!-- ===== ШАПКА ===== -->
        @include('partials.header')
        
        <!-- ===== ВКЛАДКИ ===== -->
        @include('partials.tabs')
        
        <!-- ===== ОСНОВНОЙ КОНТЕНТ ===== -->
        <main>
            @yield('content')
        </main>
        
        <!-- ===== ПОДВАЛ ===== -->
        @include('partials.footer')
        
    </div>

    <!-- ===== ФЛЕШ-СООБЩЕНИЯ ===== -->
    @if(session('success'))
        <div class="flash-message flash-success" id="flashMessage">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="flash-message flash-error" id="flashMessage">
            {{ session('error') }}
        </div>
    @endif

    <!-- ===== СКРИПТЫ ===== -->
    <script>
        // Глобальные переменные
        window.KOPI = {
            user: @json(auth()->check() ? auth()->user() : null),
            isAuth: @json(auth()->check()),
            csrf: '{{ csrf_token() }}',
            routes: {
                calculate: '{{ route("calculator.calculate") }}',
                habitsIndex: '{{ route("habits.index") }}',
                habitsStore: '{{ route("habits.store") }}',
                habitsTotal: '{{ route("habits.total") }}',
                dreamUpdate: '{{ route("dream.update") }}',
                dreamGet: '{{ route("dream.get") }}',
            },
            // Функции для URL с параметрами
            url: {
                tipsSave: function(id) {
                    return '{{ url("tips") }}/' + id + '/save';
                },
                habitsEdit: function(id) {
                    return '{{ url("habits") }}/' + id + '/edit';
                },
                habitsUpdate: function(id) {
                    return '{{ url("habits") }}/' + id;
                },
                habitsDestroy: function(id) {
                    return '{{ url("habits") }}/' + id;
                },
                historyDestroy: function(id) {
                    return '{{ url("history") }}/' + id;
                }
            }
        };
    </script>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/coins.js') }}"></script>
    <script src="{{ asset('js/calculator.js') }}"></script>
    
    @stack('scripts')
</body>
</html>