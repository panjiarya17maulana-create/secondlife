@extends('layouts.app')
@section('title', 'Verifikasi Penjual — SecondLife')
@section('content')
<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">🪪 Verifikasi Penjual</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">{{ $penjualMenunggu->total() }} penjual menunggu verifikasi KTM</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">← Kembali</a>
  </div>

  @if($penjualMenunggu->isEmpty())
    <div class="empty-state">
      <div class="empty-icon">✅</div>
      Tidak ada penjual yang menunggu verifikasi!
    </div>
  @else
    @foreach($penjualMenunggu as $user)
      <div class="verif-card" style="margin-bottom:1rem">
        <!-- Info Penjual -->
        <div class="verif-left">
          <div style="width:52px;height:52px;border-radius:50%;background:var(--amber-bg);display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;color:var(--amber);flex-shrink:0">
            {{ strtoupper(substr($user->nama,0,2)) }}
          </div>
          <div>
            <p style="font-weight:700;font-size:15px;margin-bottom:3px">{{ $user->nama }}</p>
            <p style="font-size:13px;color:var(--text2)">{{ $user->email }}</p>
            <p style="font-size:13px;color:var(--text2)">📱 {{ $user->no_hp }}</p>
            <p style="font-size:13px;color:var(--text2)">📍 {{ $user->lokasi ?? '-' }}</p>
            <p style="font-size:12px;color:var(--text2);margin-top:4px">Daftar: {{ $user->created_at?->format('d M Y, H:i') ?? '-' }}</p>
          </div>
        </div>

        <!-- Foto KTM -->
        <div class="verif-ktm">
          <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text2);margin-bottom:8px">Foto KTM</p>
          @if($user->foto_ktm)
            <a href="{{ asset('storage/ktm/'.$user->foto_ktm) }}" target="_blank">
              <img src="{{ asset('storage/ktm/'.$user->foto_ktm) }}" class="ktm-thumb" alt="KTM {{ $user->nama }}">
            </a>
            <p style="font-size:11px;color:var(--text2);margin-top:6px">Klik untuk perbesar</p>
          @else
            <div style="width:160px;height:110px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text2);font-size:13px;border:1px dashed var(--border)">
              Tidak ada foto
            </div>
          @endif
        </div>

        <!-- Aksi -->
        <div class="verif-aksi">
          <form method="POST" action="{{ route('admin.verifikasi.proses', $user->id_user) }}">
            @csrf
            <input type="hidden" name="status" value="aktif">
            <button class="btn btn-primary btn-full" style="padding:10px;font-size:14px" type="submit">✅ Verifikasi</button>
          </form>
          <form method="POST" action="{{ route('admin.verifikasi.proses', $user->id_user) }}">
            @csrf
            <input type="hidden" name="status" value="ditolak">
            <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Alasan penolakan..." style="font-size:12px;margin-bottom:6px"></textarea>
            <button class="btn btn-ghost btn-full" style="padding:10px;font-size:14px;border-color:var(--red);color:var(--red)" type="submit">❌ Tolak</button>
          </form>
        </div>
      </div>
    @endforeach

    <div style="margin-top:1.5rem">{{ $penjualMenunggu->links() }}</div>
  @endif
</div>
@endsection
