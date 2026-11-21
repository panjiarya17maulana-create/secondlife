@extends('layouts.app')
@section('title', 'Atur Ulang Password — SecondLife')
@section('content')

<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">Second<span>Life</span></div>
    <h2 class="auth-title">Password Baru</h2>
    <p class="auth-sub" style="margin-bottom: 24px;">Silakan buat kata sandi baru untuk akun Anda (<strong>{{ $email }}</strong>).</p>

    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <div class="form-group">
        <label class="form-label">Password Baru</label>
        <input class="form-control" type="password" name="password"
               placeholder="Minimal 8 karakter" required autofocus>
      </div>

      <div class="form-group" style="margin-top: 16px;">
        <label class="form-label">Konfirmasi Password Baru</label>
        <input class="form-control" type="password" name="password_confirmation"
               placeholder="Ulangi password baru" required>
      </div>

      <button class="btn btn-primary btn-full" type="submit" style="margin-top: 24px;">Simpan Kata Sandi</button>
    </form>
  </div>
</div>

@endsection
