@extends('layouts.app')
@section('title', 'Kelola Kategori — SecondLife')
@section('content')
<div class="page-section" style="max-width:800px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">🏷️ Kelola Kategori</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">{{ $kategoriList->count() }} kategori tersedia</p>
    </div>
    <div style="display:flex;gap:8px">
      <a class="btn btn-ghost" href="{{ route('admin.kategori.requests') }}" style="padding:9px 18px;font-size:14px">📨 Request Kategori</a>
      <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">← Kembali</a>
    </div>
  </div>

  <!-- TAMBAH KATEGORI -->
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.5rem">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:1rem">+ Tambah Kategori Baru</h3>
    <form method="POST" action="{{ route('admin.kategori.store') }}" style="display:flex;gap:10px">
      @csrf
      <input class="form-control" type="text" name="nama_kategori" placeholder="Nama kategori baru..." value="{{ old('nama_kategori') }}" required style="flex:1">
      <button class="btn btn-primary" type="submit" style="padding:10px 24px">Tambah</button>
    </form>
    @error('nama_kategori')
      <p style="color:var(--red);font-size:13px;margin-top:6px">{{ $message }}</p>
    @enderror
  </div>

  <!-- DAFTAR KATEGORI -->
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Nama Kategori</th><th style="text-align:right">Jumlah Produk</th><th style="text-align:right">Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($kategoriList as $k)
          <tr>
            <td style="color:var(--text2);font-size:12px">{{ $k->id_kategori }}</td>
            <td>
              <span style="font-weight:600">{{ $k->nama_kategori }}</span>
            </td>
            <td style="text-align:right">
              <span class="badge b-diproses">{{ $k->produk_count }} produk</span>
            </td>
            <td style="text-align:right">
              @if($k->produk_count == 0)
                <form method="POST" action="{{ route('admin.kategori.hapus',$k->id_kategori) }}" onsubmit="return confirm('Hapus kategori {{ $k->nama_kategori }}?')">
                  @csrf @method('DELETE')
                  <button class="btn-sm" style="border-color:var(--red);color:var(--red)" type="submit">Hapus</button>
                </form>
              @else
                <span style="font-size:12px;color:var(--text2)">Ada produk aktif</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text2)">Belum ada kategori.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
