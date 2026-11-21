@extends('layouts.app')

@section('content')
<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">pre<span>loved</span>.mhs</div>
    <h2 class="auth-title">Buat Akun Pembeli</h2>
    <p class="auth-sub">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    @if ($errors->any())
      <div class="alert alert-error">
        @foreach ($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('register.proses') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input class="form-control" type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap sesuai KTM" required>
      </div>
      
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@mahasiswa.ac.id" required>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Password</label>
          <input class="form-control" type="password" name="password" placeholder="Min 6 karakter" required>
        </div>
        <div class="form-group">
          <label class="form-label">No. WhatsApp</label>
          <input class="form-control" type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
          <small style="color:var(--text2);font-size:11px">Dipakai untuk chat COD</small>
        </div>
      </div>
      
      <div class="form-group">
        <label class="form-label">Lokasi / Kampus</label>
        <input class="form-control" type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: UPN Veteran Jawa Timur">
      </div>
      
      <button class="btn btn-primary btn-full" type="submit">Daftar Sekarang</button>
    </form>
  </div>
</div>
@endsection