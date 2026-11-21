@extends('layouts.app')
@section('title', 'Request Kategori — SecondLife')
@section('content')
<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">📨 Request Kategori</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">Usulan kategori dari pengguna</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.kategori.index') }}" style="padding:9px 18px;font-size:14px">← Kelola Kategori</a>
  </div>

  <!-- FILTER -->
  <div class="tabs" style="margin-bottom:1.5rem">
    @foreach([''=> 'Semua', 'menunggu'=>'⏳ Menunggu', 'disetujui'=>'✅ Disetujui', 'ditolak'=>'❌ Ditolak'] as $v=>$l)
      <a class="tab {{ request('status')===$v ? 'active' : '' }}"
         href="{{ route('admin.kategori.requests', $v ? ['status'=>$v] : []) }}">{{ $l }}</a>
    @endforeach
  </div>

  @forelse($list as $r)
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:.75rem">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem">
        <div style="flex:1">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <strong style="font-size:16px">{{ $r->nama_usulan }}</strong>
            @php
              $bc2 = match($r->status) {
                'disetujui' => 'b-selesai',
                'ditolak'   => 'b-dibatalkan',
                default     => 'b-menunggu',
              };
            @endphp
            <span class="badge {{ $bc2 }}">{{ $r->status }}</span>
          </div>
          <p style="font-size:13px;color:var(--text2)">
            👤 {{ $r->user->nama ?? '-' }} · 📅 {{ $r->created_at->format('d M Y, H:i') }}
          </p>
          @if($r->alasan)
            <p style="font-size:13px;margin-top:6px"><strong>Alasan:</strong> {{ $r->alasan }}</p>
          @endif
          @if($r->catatan_admin)
            <p style="font-size:13px;color:var(--text2);margin-top:4px">
              <strong>Catatan Admin:</strong> {{ $r->catatan_admin }}
            </p>
          @endif
          @if($r->kategori)
            <p style="font-size:13px;color:var(--green);margin-top:4px">
              ✅ Ditambahkan ke kategori: <strong>{{ $r->kategori->nama_kategori }}</strong>
            </p>
          @endif
        </div>

        @if($r->status === 'menunggu')
          <div style="display:flex;flex-direction:column;gap:8px;min-width:200px">
            <form method="POST" action="{{ route('admin.kategori.proses', $r->id_request) }}">
              @csrf
              <input type="hidden" name="status" value="disetujui">
              <button class="btn btn-primary btn-full" style="padding:10px;font-size:14px" type="submit">
                ✅ Setujui & Tambah Kategori
              </button>
            </form>
            <form method="POST" action="{{ route('admin.kategori.proses', $r->id_request) }}" style="display:flex;flex-direction:column;gap:6px">
              @csrf
              <input type="hidden" name="status" value="ditolak">
              <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Alasan penolakan (opsional)..." style="font-size:12px"></textarea>
              <button class="btn btn-ghost btn-full" style="padding:10px;font-size:14px;border-color:var(--red);color:var(--red)" type="submit">❌ Tolak</button>
            </form>
          </div>
        @endif
      </div>
    </div>
  @empty
    <div class="empty-state">
      <div class="empty-icon">📭</div>
      Tidak ada request kategori.
    </div>
  @endforelse

  <div style="margin-top:1.5rem">{{ $list->withQueryString()->links() }}</div>
</div>
@endsection
