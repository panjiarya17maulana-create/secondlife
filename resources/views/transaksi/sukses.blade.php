@extends('layouts.app')
@section('title', 'Pesanan Berhasil — SecondLife')
@section('content')

<div class="auth-wrap">
  <div class="auth-box" style="max-width:520px;text-align:center">

    <div style="font-size:72px;margin-bottom:1rem">🎉</div>
    <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800;margin-bottom:.5rem">
      Pesanan Berhasil Dibuat!
    </h2>
    <p style="color:var(--text2);font-size:14px;margin-bottom:1.5rem">
      Langkah selanjutnya: hubungi penjual via WhatsApp untuk konfirmasi jadwal COD.
    </p>

    @php
      $dt  = $transaksi->detail->first();
      $cod = $transaksi->jadwalCod;
      $tglFmt = $cod ? $cod->tanggal->format('d M Y') : '-';
      $waPesan = "Halo kak {$transaksi->penjual->nama}! 👋\n\nSaya *".auth()->user()->nama."* mau beli produk dari SecondLife:\n📦 *{$dt?->produk?->nama_produk}*\n💰 *{$transaksi->formatTotal()}*\n\n📅 Jadwal COD:\n• Hari : {$cod?->hari}\n• Tanggal: {$tglFmt}\n• Waktu : {$cod?->waktu} WIB\n• Lokasi: {$cod?->lokasi_cod}\n\nApakah bisa? Terima kasih! 🙏";
      $noHP = preg_replace('/[^0-9]/', '', $transaksi->penjual->no_hp ?? '');
      if (str_starts_with($noHP, '0')) $noHP = '62' . substr($noHP, 1);
      $waUrl = "https://wa.me/{$noHP}?text=" . urlencode($waPesan);
    @endphp

    <!-- RINGKASAN PESANAN -->
    <div class="cod-summary-box">
      <div class="cod-summary-row"><span>Produk</span><strong>{{ $dt?->produk?->nama_produk }}</strong></div>
      <div class="cod-summary-row"><span>Total</span><strong style="color:var(--accent)">{{ $transaksi->formatTotal() }}</strong></div>
      <div class="cod-summary-row"><span>Penjual</span><strong>{{ $transaksi->penjual->nama }}</strong></div>
      <div style="height:1px;background:var(--border);margin:.75rem 0"></div>
      @if($cod)
        <div class="cod-summary-row"><span>Jadwal</span><strong>{{ $cod->hari }}, {{ $tglFmt }}</strong></div>
        <div class="cod-summary-row"><span>Waktu</span><strong>{{ $cod->waktu }} WIB</strong></div>
        <div class="cod-summary-row"><span>Lokasi COD</span><strong>{{ $cod->lokasi_cod }}</strong></div>
      @endif
    </div>

    <!-- TOMBOL WA -->
    @if($transaksi->penjual->no_hp)
      <a class="btn-wa-besar" href="{{ $waUrl }}" target="_blank" rel="noopener">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Chat Penjual via WhatsApp Sekarang
      </a>
      <p style="font-size:12px;color:var(--text2);margin-top:.75rem">Pesan otomatis sudah disiapkan, tinggal kirim ke penjual!</p>
    @endif

    <div style="display:flex;gap:10px;margin-top:1.5rem;justify-content:center;flex-wrap:wrap">
      <a class="btn btn-ghost" href="{{ route('transaksi.index') }}" style="padding:10px 20px">📋 Lihat Transaksi</a>
      <a class="btn btn-ghost" href="{{ route('home') }}" style="padding:10px 20px">🏠 Beranda</a>
    </div>

    <div class="checkout-info" style="margin-top:1.5rem;text-align:left">
      <strong>Langkah selanjutnya:</strong>
      <div>1️⃣ Klik tombol WA di atas → pesan sudah terisi otomatis</div>
      <div>2️⃣ Kirim pesan ke penjual untuk konfirmasi jadwal</div>
      <div>3️⃣ Jika penjual tidak bisa → penjual akan balas WA untuk atur ulang</div>
      <div>4️⃣ Ketemuan di lokasi yang sudah disepakati, bayar langsung 💰</div>
    </div>
  </div>
</div>

@endsection
