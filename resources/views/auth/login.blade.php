@extends('layouts.app')
@section('title', 'Masuk — SecondLife')
@section('content')

<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">Second<span>Life</span></div>
    <h2 class="auth-title">Masuk ke Akun</h2>
    <p class="auth-sub">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>

    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email"
               placeholder="email@mahasiswa.ac.id" value="{{ old('email') }}" required>
      </div>
      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <label class="form-label">Password</label>
          <a href="{{ route('password.request') }}" style="font-size: 13px; text-decoration: none; color: var(--primary-color)">Lupa Password?</a>
        </div>
        <input class="form-control" type="password" name="password"
               placeholder="Password kamu" required>
      </div>
      <button class="btn btn-primary btn-full" type="submit">Masuk</button>
    </form>
  </div>
</div>

@endsection
