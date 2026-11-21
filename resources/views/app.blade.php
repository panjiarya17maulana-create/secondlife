<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecondLife</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">Second<span>Life</span></div>
        <div class="nav-right">
            @auth
                <span class="role-badge role-pembeli">Mahasiswa</span>
                <a href="/logout" class="nav-btn">Keluar</a>
            @else
                <a href="/login" class="btn-login-nav">Masuk</a>
            @endauth
        </div>
    </nav>
    <div class="page-wrapper">
        @yield('content')
    </div>
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-logo">Second<span>Life</span></div>
            <div class="footer-copy">&copy; {{ date('Y') }} Proyek Circular Economy Kampus</div>
        </div>
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>