@extends('layouts.app')

@section('content')
<div class="page-section" style="max-width:900px">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
        <h2 style="font-family:var(--font-display); font-size:22px; font-weight:800">📋 Verifikasi KTM Mahasiswa</h2>
        <div style="display:flex; gap:8px">
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu']) }}" class="btn {{ $filter === 'menunggu' ? 'btn-primary' : 'btn-ghost' }}" style="padding:6px 12px; font-size:13px">⏳ Menunggu</a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'aktif']) }}" class="btn {{ $filter === 'aktif' ? 'btn-primary' : 'btn-ghost' }}" style="padding:6px 12px; font-size:13px">✅ Aktif</a>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:1rem">
        @forelse($users as $u)
            <div class="verif-card">
                <div class="verif-left">
                    <div>
                        <h4 style="font-size:16px; font-weight:700; margin-bottom:4px">{{ $u->nama }}</h4>
                        <p style="font-size:13px; color:var(--text2)">📧 {{ $u->email }} | 📍 {{ $u->lokasi }}</p>
                    </div>
                </div>
                <div class="verif-ktm">
                    <img src="{{ asset('uploads/ktm/' . $u->foto_ktm) }}" class="ktm-thumb" alt="KTM">
                </div>
                <div class="verif-aksi">
                    @if($filter === 'menunggu')
                        <form action="{{ route('admin.verifikasi.proses', $u->id_user) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-primary" style="padding:6px; font-size:12px; background:var(--green)">✓ Setujui</button>
                        </form>
                        <details>
                            <summary style="cursor:pointer; color:var(--red); font-size:12px; text-align:center; margin-top:4px">✕ Tolak</summary>
                            <form action="{{ route('admin.verifikasi.proses', $u->id_user) }}" method="POST" style="margin-top:4px">
                                @csrf
                                <input type="hidden" name="action" value="tolak">
                                <textarea class="form-control" name="catatan" placeholder="Alasan ditolak..." rows="2" style="font-size:12px" required></textarea>
                                <button type="submit" class="btn" style="width:100%; padding:4px; font-size:11px; background:var(--red); color:white; margin-top:4px">Kirim</button>
                            </form>
                        </details>
                    @else
                        <span class="badge b-tersedia">Aktif Berjualan</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">📭 Tidak ada pengajuan pendaftaran yang perlu ditinjau.</div>
        @endforelse
    </div>
</div>
@endsection