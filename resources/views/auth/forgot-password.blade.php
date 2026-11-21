@extends('layouts.app')
@section('title', 'Lupa Password — SecondLife')
@section('content')

<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">Second<span>Life</span></div>
    <h2 class="auth-title">Lupa Password?</h2>
    <p class="auth-sub" style="margin-bottom: 24px;">Masukkan alamat email terdaftar Anda untuk mengatur ulang kata sandi (proses ini akan di-bypass langsung ke ubah password).</p>

    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email"
               placeholder="email@mahasiswa.ac.id" value="{{ old('email') }}" required autofocus>
      </div>
      <button class="btn btn-primary btn-full" type="submit" style="margin-top: 12px;">Kirim Link Reset</button>
    </form>
    
    <div style="text-align: center; margin-top: 20px;">
      <a href="{{ route('login') }}" style="font-size: 14px; text-decoration: none; color: var(--primary-color); font-weight: 500;">
        ← Kembali ke Halaman Masuk
      </a>
    </div>
  </div>
</div>

@endsection
