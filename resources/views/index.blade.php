@extends('layouts.app')

@section('content')
<div class="page-section" style="max-width:1100px">
    <h2 style="font-family:var(--font-display); font-size:24px; font-weight:800; margin-bottom:1.5rem">👑 Dashboard Admin</h2>

    <div class="dashboard-grid" style="margin-bottom:2rem">
        <div class="dash-card">
            <div class="dash-label">Total User</div>
            <div class="dash-value">{{ $stats['total_user'] }}</div>
            <div style="font-size:11px; color:var(--text2); margin-top:4px"> Pembeli: {{ $stats['total_pembeli'] }} | Penjual: {{ $stats['total_penjual'] }}</div>
        </div>
        <div class="dash-card">
            <div class="dash-label">Produk Aktif</div>
            <div class="dash-value" style="color:var(--accent)">{{ $stats['total_produk'] }}</div>
        </div>
        <div class="dash-card">
            <div class="dash-label">Total Transaksi</div>
            <div class="dash-value">{{ $stats['total_trx'] }}</div>
            <div style="font-size:11px; color:var(--green); font-weight:600; margin-top:4px">✅ {{ $stats['trx_selesai'] }} Selesai COD</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; align-items:start">
        <div>
            <h3 style="font-size:16px; font-weight:700; margin-bottom:1rem">📦 Transaksi Terbaru</h3>
            <div style="display:flex; flex-direction:column; gap:0.5rem">
                @foreach($trxTerbaru as $t)
                    <div class="trx-card" style="background:var(--white); padding:12px; border-radius:12px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                        <div>
                            <strong style="display:block; font-size:14px">{{ $t->nama_produk }}</strong>
                            <span style="font-size:12px; color:var(--text2)">{{ $t->pembeli }} → {{ $t->penjual }}</span>
                        </div>
                        <div style="text-align:right">
                            <div style="font-size:14px; font-weight:700; color:var(--accent)">Rp {{ number_format($t->harga_satuan, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h3 style="font-size:16px; font-weight:700; margin-bottom:1rem">👤 Mahasiswa Baru mendaftar</h3>
            <div style="display:flex; flex-direction:column; gap:0.5rem">
                @foreach($userTerbaru as $u)
                    <div class="trx-card" style="background:var(--white); padding:10px 12px; border-radius:12px; border:1px solid var(--border); display:flex; align-items:center; gap:12px">
                        <div class="seller-avatar" style="width:36px; height:36px; background:var(--accent2); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px">
                            {{ strtoupper(substr($u->nama, 0, 2)) }}
                        </div>
                        <div style="flex:1">
                            <strong style="display:block; font-size:14px">{{ $u->nama }}</strong>
                            <span style="font-size:12px; color:var(--text2)">{{ $u->email }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection