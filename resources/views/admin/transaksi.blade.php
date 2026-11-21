@extends('layouts.app')
@section('title', 'Semua Transaksi — SecondLife')
@section('content')
<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">💰 Semua Transaksi</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">Total {{ $transaksiList->total() }} transaksi</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">← Kembali</a>
  </div>

  <!-- FILTER -->
  <form method="GET" action="{{ route('admin.transaksi.index') }}" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap">
    <input class="form-control" type="text" name="cari" placeholder="🔍 Cari pembeli atau penjual..." value="{{ request('cari') }}" style="flex:1;min-width:200px">
    <select class="form-control" name="status" style="min-width:180px">
      <option value="">Semua Status</option>
      @foreach(['menunggu_konfirmasi'=>'⏳ Menunggu','dikonfirmasi'=>'✅ Dikonfirmasi','selesai'=>'🎉 Selesai','dibatalkan'=>'❌ Dibatalkan'] as $v=>$l)
        <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary" type="submit">Filter</button>
    <a class="btn btn-ghost" href="{{ route('admin.transaksi.index') }}">Reset</a>
  </form>

  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th>Produk</th><th>Pembeli</th><th>Penjual</th><th>Total</th><th>COD</th><th>Status</th><th>Tanggal</th></tr>
        </thead>
        <tbody>
          @forelse($transaksiList as $t)
            @php $p = $t->detail->first()?->produk; @endphp
            <tr>
              <td style="color:var(--text2);font-size:12px">{{ $t->id_transaksi }}</td>
              <td>
                <div style="display:flex;align-items:center;gap:8px">
                  @if($p && $p->foto->isNotEmpty())
                    <img src="{{ asset('storage/produk/'.$p->foto->first()->foto) }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px" alt="">
                  @endif
                  <span style="font-weight:600;font-size:13px">{{ $p->nama_produk ?? 'Produk dihapus' }}</span>
                </div>
              </td>
              <td style="font-size:13px">{{ $t->pembeli->nama ?? '-' }}</td>
              <td style="font-size:13px">{{ $t->penjual->nama ?? '-' }}</td>
              <td style="font-family:var(--font-display);font-weight:800;color:var(--accent)">{{ $t->formatTotal() }}</td>
              <td style="font-size:12px;color:var(--text2)">
                @if($t->jadwalCod)
                  {{ $t->jadwalCod->tanggal->format('d M Y') }}<br>
                  {{ $t->jadwalCod->waktu }} · {{ Str::limit($t->jadwalCod->lokasi_cod, 25) }}
                @else
                  <span>-</span>
                @endif
              </td>
              <td>
                @php
                  $bc = match($t->status) {
                    'selesai','dikonfirmasi' => 'b-selesai',
                    'dibatalkan'             => 'b-dibatalkan',
                    'menunggu_konfirmasi'    => 'b-menunggu',
                    default                  => 'b-diproses',
                  };
                @endphp
                <span class="badge {{ $bc }}">{{ $t->labelStatus() }}</span>
              </td>
              <td style="font-size:12px;color:var(--text2)">{{ $t->created_at->format('d M Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text2)">Tidak ada transaksi ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div style="margin-top:1.5rem">{{ $transaksiList->withQueryString()->links() }}</div>
</div>
@endsection
