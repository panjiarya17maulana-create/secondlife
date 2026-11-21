@extends('layouts.app')
@section('title', 'Kelola Produk — SecondLife')
@section('content')
<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">📦 Kelola Produk</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">Total {{ $produkList->total() }} produk aktif</p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">← Kembali</a>
  </div>

  <!-- FILTER -->
  <form method="GET" action="{{ route('admin.produk.index') }}" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap">
    <input class="form-control" type="text" name="cari" placeholder="🔍 Cari nama produk atau penjual..." value="{{ request('cari') }}" style="flex:1;min-width:200px">
    <select class="form-control" name="status" style="min-width:140px">
      <option value="">Semua Status</option>
      <option value="tersedia" {{ request('status')==='tersedia'?'selected':'' }}>Tersedia</option>
      <option value="habis"    {{ request('status')==='habis'?'selected':'' }}>Habis</option>
      <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Nonaktif</option>
    </select>
    <button class="btn btn-primary" type="submit">Filter</button>
    <a class="btn btn-ghost" href="{{ route('admin.produk.index') }}">Reset</a>
  </form>

  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr><th>Foto</th><th>Produk</th><th>Penjual</th><th>Kategori</th><th>Harga</th><th>Kondisi</th><th>Status</th><th>Upload</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($produkList as $p)
            <tr>
              <td>
                @if($p->foto->isNotEmpty())
                  <img src="{{ asset('storage/produk/'.$p->foto->first()->foto) }}" style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid var(--border)" alt="">
                @else
                  <div style="width:52px;height:52px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:1px solid var(--border)">📦</div>
                @endif
              </td>
              <td>
                <a href="{{ route('produk.detail',$p->id_produk) }}" style="font-weight:700;font-size:14px;color:var(--text);text-decoration:none;hover:color:var(--accent)">
                  {{ $p->nama_produk }}
                </a>
              </td>
              <td style="font-size:13px">{{ $p->penjual->nama ?? '-' }}</td>
              <td><span class="badge b-diproses">{{ $p->kategori->nama_kategori }}</span></td>
              <td style="font-family:var(--font-display);font-weight:800;color:var(--accent)">{{ $p->formatHarga() }}</td>
              <td><span class="badge {{ $p->kondisi==='Baru'?'b-baru':'b-bekas' }}">{{ $p->kondisi }}</span></td>
              <td>
                @if($p->status === 'tersedia')
                  <span class="badge b-tersedia">✅ Tersedia</span>
                @elseif($p->status === 'nonaktif')
                  <span class="badge" style="background:#E8E8E8;color:#666">🔒 Nonaktif</span>
                @else
                  <span class="badge b-habis">❌ Habis</span>
                @endif
              </td>
              <td style="font-size:12px;color:var(--text2)">{{ $p->created_at->format('d M Y') }}</td>
              <td>
                <div style="display:flex;gap:5px;flex-wrap:wrap">
                  {{-- Tombol Sembunyikan/Publish (toggle dari beranda) --}}
                  <form method="POST" action="{{ route('admin.produk.togglePublish',$p->id_produk) }}" style="display:inline">
                    @csrf @method('PATCH')
                    @if($p->status === 'nonaktif')
                      <button class="btn-sm" style="border-color:#3B6D11;color:#3B6D11;background:#EAF3DE" type="submit" onclick="return confirm('Publikasikan produk ini kembali?')" title="Tampilkan kembali di beranda">📢 Publish</button>
                    @else
                      <button class="btn-sm" style="border-color:#8B6914;color:#8B6914;background:#FFF8E6" type="submit" onclick="return confirm('Sembunyikan produk ini dari beranda?')" title="Sembunyikan dari beranda">👁️ Hide</button>
                    @endif
                  </form>
                  {{-- Tombol Hapus Permanen --}}
                  <form method="POST" action="{{ route('admin.produk.hapus',$p->id_produk) }}" onsubmit="return confirm('⚠️ HAPUS PERMANEN!\n\nProduk akan dihapus dari database.\nAksi ini TIDAK BISA dibatalkan.\n\nLanjutkan?')">
                    @csrf @method('DELETE')
                    <button class="btn-sm" style="border-color:var(--red);color:var(--red)" type="submit" title="Hapus permanen dari database">🗑️ Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--text2)">Tidak ada produk ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div style="margin-top:1.5rem">{{ $produkList->withQueryString()->links() }}</div>
</div>
@endsection
