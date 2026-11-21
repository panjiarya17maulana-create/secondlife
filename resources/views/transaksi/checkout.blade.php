@extends('layouts.app')
@section('title', 'Checkout — SecondLife')
@section('content')

<div class="form-page-wrap">
  <div class="form-page-box">
    <h2 class="form-page-title">📅 Atur Jadwal COD</h2>
    <p style="color:var(--text2);font-size:14px;margin-bottom:1.25rem">
      Isi jadwal ketemuan. Setelah pesan, kamu akan langsung diarahkan ke <strong>WhatsApp penjual</strong> untuk konfirmasi.
    </p>

    <!-- Info Produk -->
    <div class="checkout-produk">
      <div style="font-size:36px">
        @if($produk->foto->isNotEmpty())
          <img src="{{ asset('storage/produk/'.$produk->foto->first()->foto) }}"
               style="width:64px;height:64px;object-fit:cover;border-radius:10px" alt="">
        @else
          📦
        @endif
      </div>
      <div>
        <div style="font-weight:600;font-size:15px">{{ $produk->nama_produk }}</div>
        <div style="font-size:13px;color:var(--text2)">
          Penjual: <strong>{{ $produk->penjual->nama }}</strong> · 📍{{ $produk->penjual->lokasi }}
        </div>
        <div style="font-family:var(--font-display);font-size:22px;font-weight:800;color:var(--accent)">
          {{ $produk->formatHarga() }}
        </div>
      </div>
    </div>

    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('checkout.proses', $produk->id_produk) }}">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Hari <span class="req">*</span></label>
          <select class="form-control" name="hari" required>
            <option value="">Pilih hari...</option>
            @foreach($hariList as $h)
              <option value="{{ $h }}" {{ old('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal <span class="req">*</span></label>
          <input class="form-control" type="date" name="tanggal"
                 min="{{ date('Y-m-d') }}" value="{{ old('tanggal') }}" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Waktu <span class="req">*</span></label>
        <input class="form-control" type="time" name="waktu" value="{{ old('waktu') }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Lokasi / Titik Ketemuan <span class="req">*</span></label>
        <input class="form-control" type="text" name="lokasi_cod"
               value="{{ old('lokasi_cod') }}"
               placeholder="Contoh: Depan Perpustakaan UGM, Kantin FT lt.1" required>
      </div>

      <div class="form-group">
        <label class="form-label">Catatan <span style="color:var(--text2);font-weight:400">(opsional)</span></label>
        <textarea class="form-control" name="catatan" rows="2"
                  placeholder="Misal: Saya pakai jaket merah, siapkan kembalian...">{{ old('catatan') }}</textarea>
      </div>

      <div class="checkout-info">
        <div>💰 <strong>Sistem COD:</strong> Bayar langsung saat ketemuan.</div>
        <div>💬 Setelah pesan, kamu akan langsung diarahkan ke <strong>WhatsApp penjual</strong> untuk konfirmasi jadwal.</div>
        <div>⚠️ Jika penjual tidak bisa di waktu itu, penjual akan balas WA kamu untuk atur ulang.</div>
      </div>

      <button class="btn btn-primary btn-full" type="submit" style="margin-top:1.5rem;padding:14px;font-size:16px">
        📅 Pesan & Chat Penjual via WA
      </button>
    </form>
  </div>
</div>

@endsection
