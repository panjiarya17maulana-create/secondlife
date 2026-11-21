@extends('layouts.app')

@section('content')
<div class="auth-wrap">
  <div class="auth-box">
    <div class="auth-logo">Second<span>Life</span></div>
    <h2 class="auth-title">Masuk ke Akun</h2>
    <p class="auth-sub">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>

    @if (session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login.proses') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" placeholder="email@mahasiswa.ac.id" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" placeholder="Password kamu" required>
      </div>

      <button class="btn btn-primary btn-full" type="submit">Masuk</button>
    </form>
  </div>
</div>
@endsection