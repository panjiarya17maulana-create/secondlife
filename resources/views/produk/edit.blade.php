@extends('layouts.app')
@section('title', 'Edit Produk — SecondLife')
@section('content')

<div class="form-page-wrap">
  <div class="form-page-box">
    <h2 class="form-page-title">✏️ Edit Produk</h2>

    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('penjual.produk.update', $produk->id_produk) }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <!-- Foto existing -->
      @if($produk->foto->isNotEmpty())
        <div class="form-group">
          <label class="form-label">Foto Saat Ini <span style="color:var(--text2);font-weight:500;font-size:13px">(centang untuk hapus)</span></label>
          <div style="display:flex;gap:10px;flex-wrap:wrap">
            @foreach($produk->foto as $f)
              <label style="position:relative;cursor:pointer">
                <img src="{{ asset('storage/produk/'.$f->foto) }}"
                     style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--border)">
                <input type="checkbox" name="hapus_foto[]" value="{{ $f->id_foto }}"
                       style="position:absolute;top:4px;right:4px;width:16px;height:16px">
              </label>
            @endforeach
          </div>
        </div>
      @endif

      <div class="form-group">
        <label class="form-label">Tambah Foto Baru</label>
        <div class="upload-area" onclick="document.getElementById('fotoInput').click()">
          <input type="file" id="fotoInput" name="foto_baru[]" multiple accept="image/*" onchange="previewFotos(this)">
          <div id="fotoPreview">
            <div style="font-size:32px">📷</div>
            <div style="font-size:14px;color:var(--text2);font-weight:500;letter-spacing:0.3px">Klik untuk upload foto baru</div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Nama Produk</label>
        <input class="form-control" type="text" name="nama_produk"
               style="font-size:15px;font-weight:500;letter-spacing:0.3px"
               value="{{ old('nama_produk', $produk->nama_produk) }}" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-control" name="id_kategori" style="font-size:15px;font-weight:500">
            @foreach($kategori as $k)
              <option value="{{ $k->id_kategori }}" {{ old('id_kategori', $produk->id_kategori) == $k->id_kategori ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Kondisi</label>
          <select class="form-control" name="kondisi" style="font-size:15px;font-weight:500">
            <option value="Baru"  {{ old('kondisi', $produk->kondisi) === 'Baru'  ? 'selected' : '' }}>Baru</option>
            <option value="Bekas" {{ old('kondisi', $produk->kondisi) === 'Bekas' ? 'selected' : '' }}>Bekas</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Harga (Rp)</label>
          <div style="position:relative">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text2);font-weight:700;font-size:16px">Rp</span>
            <input class="form-control" type="text" id="harga_display"
                   style="padding-left:38px;font-size:18px;font-weight:700;letter-spacing:0.5px" oninput="formatHarga(this)"
                   value="{{ number_format(old('harga', $produk->harga),0,',','.') }}" required>
            <input type="hidden" name="harga" id="harga" value="{{ old('harga', $produk->harga) }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-control" name="status" style="font-size:15px;font-weight:500">
            <option value="tersedia" {{ old('status', $produk->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            <option value="habis"    {{ old('status', $produk->status) === 'habis'    ? 'selected' : '' }}>Habis</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" rows="5" style="font-size:15px;font-weight:500;letter-spacing:0.3px;line-height:1.6">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <button class="btn btn-primary" type="submit">💾 Simpan Perubahan</button>
        <a class="btn btn-ghost" href="{{ route('produk.detail', $produk->id_produk) }}">Batal</a>
      </div>
    </form>

    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1.5px dashed var(--border);display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <span style="font-size:14px;color:var(--text2);font-weight:700">Aksi Lainnya:</span>

        {{-- Tombol Sembunyikan / Publikasikan --}}
        <form method="POST" action="{{ route('penjual.produk.togglePublish', $produk->id_produk) }}" style="display:inline" id="publishForm">
          @csrf @method('PATCH')
          @if($produk->status === 'nonaktif')
            <button type="button" onclick="showPublishConfirmModal()" style="color:#3B6D11;border:1.5px solid #3B6D11;background:#EAF3DE;padding:10px 20px;border-radius:12px;font-weight:600;font-size:14px;cursor:pointer">📢 Publikasikan</button>
          @else
            <button type="button" onclick="showHideConfirmModal()" style="color:#8B6914;border:1.5px solid #8B6914;background:#FFF8E6;padding:10px 20px;border-radius:12px;font-weight:600;font-size:14px;cursor:pointer">👁️‍🗨️ Sembunyikan dari Beranda</button>
          @endif
        </form>

        {{-- Tombol Hapus Permanen --}}
        <form method="POST" action="{{ route('penjual.produk.hapus', $produk->id_produk) }}" style="display:inline" id="hapusForm">
          @csrf @method('DELETE')
          <button type="button" onclick="showDeleteConfirmModal()" style="color:var(--red);border:1.5px solid var(--red);background:#fff0f0;padding:10px 20px;border-radius:12px;font-weight:600;font-size:14px;cursor:pointer">🗑️ Hapus Permanen</button>
        </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function formatHarga(input) {
  const raw = input.value.replace(/[^0-9]/g, '');
  input.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
  document.getElementById('harga').value = raw ? parseInt(raw) : '';
}

function previewFotos(input) {
  const prev = document.getElementById('fotoPreview');
  if (!input.files.length) return;
  prev.innerHTML = '';
  const wrap = document.createElement('div');
  wrap.style.cssText = 'display:flex;gap:8px;flex-wrap:wrap;justify-content:center;padding:8px 0';
  prev.appendChild(wrap);
  Array.from(input.files).slice(0, 5).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--border)';
      wrap.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
  const info = document.createElement('div');
  info.style.cssText = 'margin-top:8px;font-size:14px;color:var(--text2);font-weight:500;letter-spacing:0.3px';
  info.textContent = input.files.length + ' foto dipilih';
  prev.appendChild(info);
}

function showDeleteConfirmModal() {
  const modal = document.createElement('div');
  modal.id = 'deleteConfirmModal';
  modal.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
  `;
  
  const content = document.createElement('div');
  content.style.cssText = `
    background: #ffffff;
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
    border: 2px solid #ffebee;
    animation: slideUp 0.3s ease;
  `;
  
  const icon = document.createElement('div');
  icon.style.cssText = 'font-size: 60px; margin-bottom: 20px; animation: bounce 0.6s ease;';
  icon.textContent = '⚠️';
  
  const title = document.createElement('h3');
  title.style.cssText = 'font-size: 20px; font-weight: 800; color: #c41c3b; margin: 0 0 12px 0; letter-spacing: 0.5px;';
  title.textContent = 'HAPUS PERMANEN';
  
  const message = document.createElement('p');
  message.style.cssText = 'font-size: 15px; color: #555; line-height: 1.8; margin: 0 0 32px 0; font-weight: 500;';
  message.textContent = 'Produk akan dihapus dari database beserta semua foto. Aksi ini TIDAK BISA dibatalkan.';
  
  const buttons = document.createElement('div');
  buttons.style.cssText = 'display: flex; gap: 12px; justify-content: center;';
  
  const cancelBtn = document.createElement('button');
  cancelBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #ddd;
    background: #f5f5f5;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: #333;
    transition: all 0.3s;
  `;
  cancelBtn.textContent = 'BATAL';
  cancelBtn.onmouseover = () => cancelBtn.style.background = '#e8e8e8';
  cancelBtn.onmouseout = () => cancelBtn.style.background = '#f5f5f5';
  cancelBtn.onclick = () => modal.remove();
  
  const confirmBtn = document.createElement('button');
  confirmBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #c41c3b;
    background: linear-gradient(135deg, #c41c3b 0%, #e63946 100%);
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(196, 28, 59, 0.3);
  `;
  confirmBtn.textContent = '🗑️ HAPUS SEKARANG';
  confirmBtn.onmouseover = () => {
    confirmBtn.style.transform = 'translateY(-2px)';
    confirmBtn.style.boxShadow = '0 6px 20px rgba(196, 28, 59, 0.5)';
  };
  confirmBtn.onmouseout = () => {
    confirmBtn.style.transform = 'translateY(0)';
    confirmBtn.style.boxShadow = '0 4px 12px rgba(196, 28, 59, 0.3)';
  };
  confirmBtn.onclick = () => {
    modal.remove();
    document.getElementById('hapusForm').submit();
  };
  
  buttons.appendChild(cancelBtn);
  buttons.appendChild(confirmBtn);
  
  // Add animation styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes bounce {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }
  `;
  document.head.appendChild(style);
  
  content.appendChild(icon);
  content.appendChild(title);
  content.appendChild(message);
  content.appendChild(buttons);
  modal.appendChild(content);
  
  document.body.appendChild(modal);
}

function showPublishConfirmModal() {
  const modal = document.createElement('div');
  modal.id = 'publishConfirmModal';
  modal.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
  `;
  
  const content = document.createElement('div');
  content.style.cssText = `
    background: #ffffff;
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
    border: 2px solid #EAF3DE;
    animation: slideUp 0.3s ease;
  `;
  
  const icon = document.createElement('div');
  icon.style.cssText = 'font-size: 60px; margin-bottom: 20px; animation: bounce 0.6s ease;';
  icon.textContent = '📢';
  
  const title = document.createElement('h3');
  title.style.cssText = 'font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0; letter-spacing: 0.5px;';
  title.textContent = 'PUBLIKASIKAN PRODUK';
  
  const message = document.createElement('p');
  message.style.cssText = 'font-size: 15px; color: #555; line-height: 1.8; margin: 0 0 32px 0; font-weight: 500;';
  message.textContent = 'Publikasikan produk ini kembali ke beranda?';
  
  const buttons = document.createElement('div');
  buttons.style.cssText = 'display: flex; gap: 12px; justify-content: center;';
  
  const cancelBtn = document.createElement('button');
  cancelBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #ddd;
    background: #f5f5f5;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: #333;
    transition: all 0.3s;
  `;
  cancelBtn.textContent = 'BATAL';
  cancelBtn.onmouseover = () => cancelBtn.style.background = '#e8e8e8';
  cancelBtn.onmouseout = () => cancelBtn.style.background = '#f5f5f5';
  cancelBtn.onclick = () => modal.remove();
  
  const confirmBtn = document.createElement('button');
  confirmBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #3B6D11;
    background: linear-gradient(135deg, #3B6D11 0%, #4A8A15 100%);
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(59, 109, 17, 0.3);
  `;
  confirmBtn.textContent = '📢 PUBLIKASIKAN';
  confirmBtn.onmouseover = () => {
    confirmBtn.style.transform = 'translateY(-2px)';
    confirmBtn.style.boxShadow = '0 6px 20px rgba(59, 109, 17, 0.5)';
  };
  confirmBtn.onmouseout = () => {
    confirmBtn.style.transform = 'translateY(0)';
    confirmBtn.style.boxShadow = '0 4px 12px rgba(59, 109, 17, 0.3)';
  };
  confirmBtn.onclick = () => {
    modal.remove();
    document.getElementById('publishForm').submit();
  };
  
  buttons.appendChild(cancelBtn);
  buttons.appendChild(confirmBtn);
  
  // Add animation styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes bounce {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }
  `;
  document.head.appendChild(style);
  
  content.appendChild(icon);
  content.appendChild(title);
  content.appendChild(message);
  content.appendChild(buttons);
  modal.appendChild(content);
  
  document.body.appendChild(modal);
}

function showHideConfirmModal() {
  const modal = document.createElement('div');
  modal.id = 'hideConfirmModal';
  modal.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
  `;
  
  const content = document.createElement('div');
  content.style.cssText = `
    background: #ffffff;
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
    border: 2px solid #FFF8E6;
    animation: slideUp 0.3s ease;
  `;
  
  const icon = document.createElement('div');
  icon.style.cssText = 'font-size: 60px; margin-bottom: 20px; animation: bounce 0.6s ease;';
  icon.textContent = '👁️‍🗨️';
  
  const title = document.createElement('h3');
  title.style.cssText = 'font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0; letter-spacing: 0.5px;';
  title.textContent = 'SEMBUNYIKAN PRODUK';
  
  const message = document.createElement('p');
  message.style.cssText = 'font-size: 15px; color: #555; line-height: 1.8; margin: 0 0 32px 0; font-weight: 500;';
  message.textContent = 'Produk tidak akan dihapus, hanya disembunyikan dari beranda.';
  
  const buttons = document.createElement('div');
  buttons.style.cssText = 'display: flex; gap: 12px; justify-content: center;';
  
  const cancelBtn = document.createElement('button');
  cancelBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #ddd;
    background: #f5f5f5;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: #333;
    transition: all 0.3s;
  `;
  cancelBtn.textContent = 'BATAL';
  cancelBtn.onmouseover = () => cancelBtn.style.background = '#e8e8e8';
  cancelBtn.onmouseout = () => cancelBtn.style.background = '#f5f5f5';
  cancelBtn.onclick = () => modal.remove();
  
  const confirmBtn = document.createElement('button');
  confirmBtn.style.cssText = `
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #8B6914;
    background: linear-gradient(135deg, #8B6914 0%, #A68620 100%);
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(139, 105, 20, 0.3);
  `;
  confirmBtn.textContent = '👁️‍🗨️ SEMBUNYIKAN';
  confirmBtn.onmouseover = () => {
    confirmBtn.style.transform = 'translateY(-2px)';
    confirmBtn.style.boxShadow = '0 6px 20px rgba(139, 105, 20, 0.5)';
  };
  confirmBtn.onmouseout = () => {
    confirmBtn.style.transform = 'translateY(0)';
    confirmBtn.style.boxShadow = '0 4px 12px rgba(139, 105, 20, 0.3)';
  };
  confirmBtn.onclick = () => {
    modal.remove();
    document.getElementById('publishForm').submit();
  };
  
  buttons.appendChild(cancelBtn);
  buttons.appendChild(confirmBtn);
  
  content.appendChild(icon);
  content.appendChild(title);
  content.appendChild(message);
  content.appendChild(buttons);
  
  // Add animation styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes bounce {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }
  `;
  document.head.appendChild(style);
  
  modal.appendChild(content);
  document.body.appendChild(modal);
}
</script>
@endpush
