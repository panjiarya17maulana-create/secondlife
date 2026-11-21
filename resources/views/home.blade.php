@extends('layouts.app')
@section('title', 'SecondLife — Jual Beli Barang Mahasiswa')
@section('content')

<!-- HERO -->
<section class="hero">
  <div class="hero-text">
    <h1>Jual Beli Barang<br><span class="accent">Sesama Mahasiswa</span></h1>
    <p>Platform SecondLife khusus mahasiswa. Temukan buku, elektronik, alat tulis, dan perlengkapan kuliah harga terjangkau.</p>
  </div>
  <div class="hero-stats">
    <div class="stat"><div class="stat-num">{{ number_format($totalProduk) }}</div><div class="stat-label">Produk Aktif</div></div>
    <div class="stat"><div class="stat-num">847</div><div class="stat-label">Mahasiswa</div></div>
    <div class="stat"><div class="stat-num">3.4K</div><div class="stat-label">Transaksi</div></div>
  </div>
</section>

<!-- SEARCH -->
<div class="search-wrap" id="produk">
  <form class="search-row" method="GET" action="{{ route('home') }}">
    <input class="search-input" type="text" name="q" placeholder="Cari produk atau penjual..." value="{{ $q }}">
    <select class="filter-select" name="kategori">
      <option value="">Semua Kategori</option>
      @foreach($kategoriList as $k)
        <option value="{{ $k->id_kategori }}" {{ $katId == $k->id_kategori ? 'selected' : '' }}>
          {{ $k->nama_kategori }}
        </option>
      @endforeach
    </select>
    <button class="btn btn-primary" type="submit">Cari</button>
  </form>
</div>

<!-- MAIN LAYOUT -->
<div class="main-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">

    <div class="sidebar-box">
      <div class="sidebar-title">Kategori</div>
      <a class="cat-item {{ !$katId ? 'active' : '' }}"
         href="{{ route('home', array_merge(request()->except('kategori','page'), ['kategori' => ''])) }}">
        <span>Semua</span>
        <span class="cat-count">{{ $totalProduk }}</span>
      </a>
      @foreach($kategoriList as $k)
        <a class="cat-item {{ $katId == $k->id_kategori ? 'active' : '' }}"
           href="{{ route('home', array_merge(request()->except('kategori','page'), ['kategori' => $k->id_kategori])) }}">
          <span>{{ $k->nama_kategori }}</span>
          <span class="cat-count">{{ $k->produk_count }}</span>
        </a>
      @endforeach
    </div>

    <div class="sidebar-box">
      <div class="sidebar-title">Rentang Harga</div>
      <form method="GET" action="{{ route('home') }}">
        @foreach(request()->except(['min_harga','max_harga']) as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <div class="price-row">
          <input class="price-input" type="number" name="min_harga" placeholder="Min" value="{{ $minHarga ?: '' }}">
          <span style="color:var(--text2)">—</span>
          <input class="price-input" type="number" name="max_harga" placeholder="Max" value="{{ $maxHarga ?: '' }}">
        </div>
        <button class="btn btn-ghost btn-full" type="submit" style="margin-top:10px;font-size:13px">Terapkan</button>
      </form>
    </div>

    <div class="sidebar-box">
      <div class="sidebar-title">Kondisi Barang</div>
      <div class="cond-btns">
        @foreach(['' => 'Semua', 'Baru' => 'Baru', 'Bekas' => 'Bekas'] as $val => $label)
          <a class="cond-btn {{ $kondisi === $val ? 'active' : '' }}"
             href="{{ route('home', array_merge(request()->except('kondisi','page'), ['kondisi' => $val])) }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

  </aside>

  <!-- PRODUK SECTION -->
  <section class="products-section">
    <div class="prod-header">
      <h2>{{ $produkList->count() }} Produk Ditemukan</h2>
      <form method="GET" action="{{ route('home') }}" style="display:inline">
        @foreach(request()->except('sort') as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <select class="sort-select" name="sort" onchange="this.form.submit()">
          <option value=""         {{ !$sort           ? 'selected' : '' }}>Terbaru</option>
          <option value="termurah" {{ $sort==='termurah' ? 'selected' : '' }}>Termurah</option>
          <option value="termahal" {{ $sort==='termahal' ? 'selected' : '' }}>Termahal</option>
        </select>
      </form>
    </div>

    @if($produkList->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">😔</div>
        Tidak ada produk ditemukan.
        <a href="{{ route('home') }}" style="color:var(--accent);font-weight:600;display:block;margin-top:.5rem">Reset filter</a>
      </div>
    @else
      <div class="product-grid">
        @foreach($produkList as $p)
          <a class="pcard" href="{{ route('produk.detail', $p->id_produk) }}">
            <div class="pcard-img">
              @if($p->foto->isNotEmpty())
                <img src="{{ asset('storage/produk/'.$p->foto->first()->foto) }}" alt="{{ $p->nama_produk }}" loading="lazy">
              @else
                📦
              @endif
            </div>
            <div class="pcard-body">
              <div class="pcard-cat">{{ $p->kategori->nama_kategori }}</div>
              <div class="pcard-name">{{ $p->nama_produk }}</div>
              <div class="pcard-price">{{ $p->formatHarga() }}</div>
              <div class="pcard-badges">
                <span class="badge {{ $p->kondisi === 'Baru' ? 'b-baru' : 'b-bekas' }}">{{ $p->kondisi }}</span>
                <span class="badge {{ $p->status === 'tersedia' ? 'b-tersedia' : 'b-habis' }}">
                  {{ $p->status === 'tersedia' ? 'Tersedia' : 'Habis' }}
                </span>
              </div>
              <div class="pcard-foot">
                <span>⭐ {{ $p->penjual->avgSellerRating() }} · {{ $p->penjual->nama }}</span>
                <span>📍{{ $p->penjual->lokasi ?? '' }}</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @endif

  </section>
</div>

@endsection
