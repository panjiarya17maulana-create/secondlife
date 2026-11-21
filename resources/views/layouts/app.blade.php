<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SecondLife')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  @stack('styles')
</head>
<body>

<nav class="navbar">
  <a class="nav-logo" href="{{ route('home') }}">Second<span>Life</span></a>

  <div class="nav-links">
    <a class="nav-btn {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>

    @auth
      @if(auth()->user()->isAdmin())
        <a class="nav-btn" href="{{ route('admin.dashboard') }}">⚙️ Admin Panel</a>
      @else
        <a class="nav-btn {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">Transaksi</a>
        @if(auth()->user()->isPenjual())
          @if(auth()->user()->status_akun === 'menunggu')
            {{-- Penjual menunggu verifikasi: tampilkan badge info, sembunyikan menu penjual --}}
            <span class="nav-btn" style="cursor:default;opacity:0.7;font-size:13px">⏳ Menunggu Verifikasi</span>
          @elseif(auth()->user()->status_akun === 'ditolak')
            <span class="nav-btn" style="cursor:default;color:var(--red);font-size:13px">❌ Akun Ditolak</span>
          @else
            <a class="nav-btn {{ request()->routeIs('penjual.*') ? 'active' : '' }}" href="{{ route('penjual.dashboard') }}">📊 Dashboard Saya</a>
            <a class="nav-btn btn-jual-nav" href="{{ route('penjual.produk.tambah') }}">+ Jual Barang</a>
          @endif
        @else
          <a class="nav-btn btn-upgrade-nav" href="{{ route('daftar.penjual') }}">🏪 Jadi Penjual</a>
        @endif
      @endif
    @endauth
  </div>

  <div class="nav-right">
    @auth
      @if(auth()->user()->isAdmin())
        <a class="avatar-nav" href="{{ route('admin.dashboard') }}" title="Admin Panel">
          {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
        </a>
      @else
        <a class="avatar-nav" href="{{ route('profil') }}" title="Profil">
          {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
        </a>
      @endif
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button class="btn-sm" type="submit">Keluar</button>
      </form>
    @else
      <div class="nav-auth">
        <a class="btn-login-nav" href="{{ route('login') }}">Masuk</a>
        <a class="btn-register-nav" href="{{ route('register') }}">Daftar</a>
      </div>
    @endauth
  </div>
</nav>

<div class="page-wrapper">

@if(session('success'))
  <div style="max-width:1400px;margin:0 auto;padding:1rem 2rem 0">
    <div class="flash alert-success">{{ session('success') }}</div>
  </div>
@endif
@if(session('error'))
  <div style="max-width:1400px;margin:0 auto;padding:1rem 2rem 0">
    <div class="flash alert-error">{{ session('error') }}</div>
  </div>
@endif

{{-- NOTIFIKASI PENOLAKAN PENJUAL --}}
@auth
  @if(auth()->user()->catatan_admin && !auth()->user()->isPenjual())
    <div style="max-width:1400px;margin:.75rem auto 0;padding:0 2rem">
      <div style="background:#fff4f4;border:1.5px solid #f5c6c6;border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:1rem;position:relative">
        <div style="font-size:22px;line-height:1;flex-shrink:0">❌</div>
        <div style="flex:1">
          <p style="font-weight:700;font-size:14px;color:#c0392b;margin-bottom:4px">
            Pendaftaran Penjual Kamu Ditolak
          </p>
          <p style="font-size:13px;color:#7f3030;margin-bottom:8px">
            <strong>Alasan dari admin:</strong> {{ auth()->user()->catatan_admin }}
          </p>
          <a href="{{ route('daftar.penjual') }}"
             style="display:inline-block;font-size:13px;font-weight:600;color:#c0392b;text-decoration:underline">
            🔄 Daftar ulang sebagai penjual
          </a>
        </div>
        <form method="POST" action="{{ route('notif.dismiss.tolak_penjual') }}" style="flex-shrink:0">
          @csrf
          <button type="submit"
                  title="Tutup notifikasi"
                  style="background:none;border:none;cursor:pointer;font-size:18px;color:#c0392b;line-height:1;padding:2px 4px">
            ×
          </button>
        </form>
      </div>
    </div>
  @endif
@endauth

@yield('content')

</div><!-- /page-wrapper -->

<footer class="footer">
  <div class="footer-inner">
    <span class="footer-logo">Second<span>Life</span></span>
    <span class="footer-copy">© {{ date('Y') }} · SecondLife — Jual Beli Barang Mahasiswa · Sistem COD · Chat via WhatsApp</span>
  </div>
</footer>

<div id="toast"></div>
@stack('scripts')
</body>
</html>
