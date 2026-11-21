@extends('layouts.app')
@section('title', 'Riwayat Transaksi — SecondLife')
@section('content')

<div class="page-section">
  <h2>Riwayat Transaksi</h2>

  <!-- TAB FILTER -->
  <div class="tabs" style="margin-bottom:1.5rem">
    @foreach(['' => 'Semua', 'menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
      <a class="tab {{ $filter === $val ? 'active' : '' }}"
         href="{{ route('transaksi.index', $val ? ['status' => $val] : []) }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <!-- PEMBELIAN -->
  <h3 style="font-size:16px;font-weight:700;margin-bottom:1rem">🛒 Pembelian Saya</h3>

  @forelse($transaksiList as $t)
    @php $dt = $t->detail->first(); $produk = $dt?->produk; @endphp
    <div class="trx-card">
      <div class="trx-icon">
        @if($produk && $produk->foto->isNotEmpty())
          <img src="{{ asset('storage/produk/'.$produk->foto->first()->foto) }}"
               style="width:48px;height:48px;object-fit:cover;border-radius:8px" alt="">
        @else
          📦
        @endif
      </div>
      <div class="trx-info">
        <strong>{{ $produk->nama_produk ?? 'Produk dihapus' }}</strong>
        <span>Penjual: {{ $t->penjual->nama ?? '-' }} · #{{ $t->id_transaksi }}</span>
      </div>
      <div class="trx-right">
        <a class="btn-sm" href="{{ route('transaksi.detail', $t->id_transaksi) }}"
           style="display:block;margin-bottom:6px;text-align:center">🔍 Detail</a>
        <div class="trx-price">{{ $t->formatTotal() }}</div>
        <span class="badge b-{{ $t->status }}">{{ $t->status }}</span>
        <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
          @if(in_array($t->status, ['menunggu_konfirmasi','dikonfirmasi']))
            <form method="POST" action="{{ route('transaksi.aksi', $t->id_transaksi) }}" style="display:inline">
              @csrf
              <input type="hidden" name="action" value="dibatalkan">
              <button class="btn-sm" style="color:var(--red)"
                      onclick="return confirm('Batalkan transaksi ini?')" type="submit">Batalkan</button>
            </form>
          @endif
          @if($t->status === 'dikonfirmasi')
            <form method="POST" action="{{ route('transaksi.aksi', $t->id_transaksi) }}" style="display:inline">
              @csrf
              <input type="hidden" name="action" value="selesai">
              <button class="btn-sm" type="submit">✅ Selesai</button>
            </form>
          @endif
          @if($t->status === 'selesai' && $produk)
            <a class="btn-sm" href="{{ route('produk.detail', $produk->id_produk) }}#review">⭐ Ulasan</a>
          @endif
        </div>
      </div>
    </div>
  @empty
    <div class="empty-state"><div class="empty-icon">📭</div>Belum ada transaksi pembelian.</div>
  @endforelse

  {{-- PENJUALAN --}}
  @if($transaksiPenjual->isNotEmpty() || true)
    <h3 style="font-size:16px;font-weight:700;margin:2rem 0 1rem">📦 Penjualan Saya</h3>

    @forelse($transaksiPenjual as $t)
      @php $dt = $t->detail->first(); $produk = $dt?->produk; @endphp
      <div class="trx-card">
        <div class="trx-icon">
          @if($produk && $produk->foto->isNotEmpty())
            <img src="{{ asset('storage/produk/'.$produk->foto->first()->foto) }}"
                 style="width:48px;height:48px;object-fit:cover;border-radius:8px" alt="">
          @else
            📦
          @endif
        </div>
        <div class="trx-info">
          <strong>{{ $produk->nama_produk ?? 'Produk dihapus' }}</strong>
          <span>Pembeli: {{ $t->pembeli->nama ?? '-' }} · #{{ $t->id_transaksi }}</span>
        </div>
        <div class="trx-right">
          <a class="btn-sm" href="{{ route('transaksi.detail', $t->id_transaksi) }}"
             style="display:block;margin-bottom:6px;text-align:center">🔍 Detail</a>
          <div class="trx-price">{{ $t->formatTotal() }}</div>
          <span class="badge b-{{ $t->status }}">{{ $t->status }}</span>
          <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
            @if($t->status === 'menunggu_konfirmasi')
              <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status_baru" value="dikonfirmasi">
                <button class="btn-sm" type="submit">✅ Konfirmasi</button>
              </form>
              <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status_baru" value="dibatalkan">
                <button class="btn-sm" style="color:var(--red)" type="submit"
                        onclick="return confirm('Tolak transaksi ini?')">❌ Tolak</button>
              </form>
            @endif
            @if($t->status === 'dikonfirmasi')
              <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status_baru" value="selesai">
                <button class="btn-sm" type="submit">🎉 Selesai</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state"><div class="empty-icon">📭</div>Belum ada transaksi penjualan.</div>
    @endforelse
  @endif

</div>

@endsection
