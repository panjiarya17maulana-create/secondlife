@extends('layouts.app')
@section('title', 'Request Kategori — SecondLife')
@section('content')
<div class="page-section">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">📨 Request Kategori Baru</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">Usulkan kategori produk baru kepada admin</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('home') }}" style="padding:9px 18px;font-size:14px">← Beranda</a>
  </div>

  {{-- FORM REQUEST --}}
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:2rem">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:1rem">✏️ Usulkan Kategori Baru</h3>
    <form method="POST" action="{{ route('request.kategori.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nama Kategori yang Diusulkan <span style="color:var(--red)">*</span></label>
        <input type="text" name="nama_usulan" class="form-control @error('nama_usulan') is-invalid @enderror"
               value="{{ old('nama_usulan') }}"
               placeholder="Contoh: Elektronik, Buku, Pakaian..." maxlength="100" required>
        @error('nama_usulan')
          <div style="color:var(--red);font-size:13px;margin-top:4px">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <label class="form-label">Alasan / Penjelasan <span style="color:var(--text2);font-weight:400">(opsional)</span></label>
        <textarea name="alasan" class="form-control" rows="3"
                  placeholder="Jelaskan kenapa kategori ini dibutuhkan...">{{ old('alasan') }}</textarea>
      </div>
      <button class="btn btn-primary" type="submit">📨 Kirim Request</button>
    </form>
  </div>

  {{-- RIWAYAT REQUEST --}}
  <h3 style="font-size:17px;font-weight:700;margin-bottom:1rem">📋 Riwayat Request Saya</h3>

  @forelse($list as $r)
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:.75rem">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem">
        <div style="flex:1">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <strong style="font-size:15px">{{ $r->nama_usulan }}</strong>
            @php
              $badgeClass = match($r->status) {
                'disetujui' => 'b-selesai',
                'ditolak'   => 'b-dibatalkan',
                default     => 'b-menunggu',
              };
              $statusLabel = match($r->status) {
                'disetujui' => '✅ Disetujui',
                'ditolak'   => '❌ Ditolak',
                default     => '⏳ Menunggu',
              };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
          </div>

          <p style="font-size:13px;color:var(--text2)">
            📅 {{ $r->created_at->format('d M Y, H:i') }}
          </p>

          @if($r->alasan)
            <p style="font-size:13px;margin-top:6px"><strong>Alasan:</strong> {{ $r->alasan }}</p>
          @endif

          @if($r->catatan_admin)
            <p style="font-size:13px;color:var(--text2);margin-top:4px">
              💬 <strong>Catatan Admin:</strong> {{ $r->catatan_admin }}
            </p>
          @endif

          @if($r->kategori)
            <p style="font-size:13px;color:var(--green);margin-top:4px">
              ✅ Ditambahkan sebagai kategori: <strong>{{ $r->kategori->nama_kategori }}</strong>
            </p>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      Kamu belum pernah mengajukan request kategori.
    </div>
  @endforelse

</div>
@endsection
