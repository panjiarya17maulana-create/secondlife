@extends('layouts.app')
@section('title', 'Daftar — SecondLife')
@section('content')

<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">Second<span>Life</span></div>
    <h2 class="auth-title">Buat Akun</h2>
    <p class="auth-sub">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input class="form-control" type="text" name="nama" value="{{ old('nama') }}"
               placeholder="Nama lengkap" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email') }}"
               placeholder="email@mahasiswa.ac.id" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Password</label>
          <input class="form-control" type="password" name="password" placeholder="Min 6 karakter" required>
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi Password</label>
          <input class="form-control" type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">No. WhatsApp</label>
          <input class="form-control" type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
        </div>
        <div class="form-group">
          <label class="form-label">Lokasi / Kampus</label>
          <input class="form-control" type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="UGM Yogyakarta">
        </div>
      </div>
      <button class="btn btn-primary btn-full" type="submit">Daftar Sekarang</button>
    </form>
  </div>
</div>

@endsection
