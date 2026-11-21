@extends('layouts.app')

@section('content')
<div class="form-page-wrap">
  <div class="form-page-box">
    <h2 class="form-page-title">📦 Jual Barang Baru</h2>

    @if ($errors->any())
      <div class="alert alert-error">
        @foreach ($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label class="form-label">Foto Produk <span style="color:var(--text2);font-weight:500;font-size:13px">(maks 5 foto, masing-masing 2MB)</span></label>
        <div class="upload-area" onclick="document.getElementById('fotoInput').click()">
          <input type="file" id="fotoInput" name="fotos[]" multiple accept="image/*" onchange="previewFotos(this)">
          <div id="fotoPreview">
            <div style="font-size:36px">📷</div>
            <div style="font-size:14px;color:var(--text2);font-weight:500;letter-spacing:0.3px">Klik untuk pilih foto</div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Nama Produk</label>
        <input class="form-control" type="text" name="nama_produk" 
               placeholder="Contoh: Buku Kalkulus Purcell Edisi 9" 
               style="font-size:15px;font-weight:500;letter-spacing:0.3px"
               value="{{ old('nama_produk') }}" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control" name="id_kategori" style="font-size:15px;font-weight:500" required>
            <option value="">Pilih kategori...</option>
            @foreach ($kategoriList as $k)
              <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Kondisi</label>
          <select class="form-control" name="kondisi" style="font-size:15px;font-weight:500">
            <option value="Baru" {{ old('kondisi') === 'Baru' ? 'selected' : '' }}>Baru</option>
            <option value="Bekas" {{ old('kondisi', 'Bekas') === 'Bekas' ? 'selected' : '' }}>Bekas</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Harga (Rp)</label>
        <div style="position:relative">
          <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text2);font-weight:700;font-size:16px">Rp</span>
          <input class="form-control" type="text" id="harga_display" placeholder="0" value="{{ old('harga') ? number_format(old('harga'),0,',','.') : '' }}" style="padding-left:38px;font-size:18px;font-weight:700;letter-spacing:0.5px" oninput="formatHarga(this)" required>
          <input type="hidden" name="harga" id="harga" value="{{ old('harga') ?: '' }}">
        </div>
        <small style="color:var(--text2);font-size:11px" id="harga_terbilang"></small>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi Produk</label>
        <textarea class="form-control" name="deskripsi" rows="5" 
                  style="font-size:15px;font-weight:500;letter-spacing:0.3px;line-height:1.6"
                  placeholder="Jelaskan kondisi barang, alasan jual, kelengkapan, dll...">{{ old('deskripsi') }}</textarea>
      </div>

      <div style="display:flex;gap:12px">
        <button class="btn btn-primary" type="submit">🚀 Posting Sekarang</button>
        <a class="btn btn-ghost" href="/index">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
function formatHarga(input) {
  let raw = input.value.replace(/[^0-9]/g, '');
  input.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
  // keep hidden numeric value in sync for form submission
  const hidden = document.getElementById('harga');
  if (hidden) hidden.value = raw ? parseInt(raw) : '';
  const n = parseInt(raw) || 0;
  const el = document.getElementById('harga_terbilang');
  if (n >= 1000000000) el.textContent = '= Rp ' + (n/1000000000).toFixed(1).replace('.',',') + ' miar';
  else if (n >= 1000000) el.textContent = '= Rp ' + (n/1000000).toFixed(1).replace('.',',') + ' juta';
  else if (n >= 1000) el.textContent = '= Rp ' + (n/1000).toFixed(0) + ' ribu';
  else el.textContent = '';
}
function previewFotos(input) {
  const prev = document.getElementById('fotoPreview');
  if (!input.files.length) return;
  prev.innerHTML = '';
  const wrap = document.createElement('div');
  wrap.style.cssText = 'display:flex;gap:8px;flex-wrap:wrap;justify-content:center';
  Array.from(input.files).slice(0, 5).forEach(file => {
    const url = URL.createObjectURL(file);
    const img = document.createElement('img');
    img.src = url;
    img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px';
    wrap.appendChild(img);
  });
  prev.appendChild(wrap);
  const info = document.createElement('div');
  info.style.cssText = 'font-size:14px;color:var(--text2);margin-top:8px;font-weight:500;letter-spacing:0.3px';
  info.textContent = input.files.length + ' foto dipilih';
  prev.appendChild(info);
}
</script>
@endsection