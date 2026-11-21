@extends('layouts.app')
@section('title', $produk->nama_produk . ' — SecondLife')
@section('content')

<style>
.detail-foto .foto-main {
  width: 100%;
  max-width: 500px;
  height: auto;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid var(--border);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.detail-foto .foto-main:hover {
  transform: scale(1.02);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.foto-thumbs {
  display: flex;
  gap: 10px;
  margin-top: 12px;
  overflow-x: auto;
  padding: 4px 0;
}

.foto-thumb {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--border);
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.foto-thumb:hover {
  border-color: var(--accent);
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#photoModal img {
  animation: zoomIn 0.3s ease;
}

@keyframes zoomIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@media (max-width: 768px) {
  .detail-foto .foto-main {
    max-width: 100%;
  }
  
  .foto-thumb {
    width: 60px;
    height: 60px;
  }
}
</style>

<div class="detail-wrap">

  <!-- BREADCRUMB -->
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Beranda</a> /
    <a href="{{ route('home', ['kategori' => $produk->id_kategori]) }}">{{ $produk->kategori->nama_kategori }}</a> /
    <span>{{ $produk->nama_produk }}</span>
  </div>

  <div class="detail-grid">

    <!-- KIRI: Foto -->
    <div class="detail-left">
      <div class="detail-foto">
        @if($produk->foto->isNotEmpty())
          <img src="{{ asset('storage/produk/'.$produk->foto->first()->foto) }}"
               alt="{{ $produk->nama_produk }}" class="foto-main" id="fotoMain"
               onclick="openPhotoModal(this.src)" style="cursor:pointer;transition:all 0.3s">
          @if($produk->foto->count() > 1)
            <div class="foto-thumbs">
              @foreach($produk->foto as $foto)
                <img src="{{ asset('storage/produk/'.$foto->foto) }}"
                     alt="" class="foto-thumb"
                     onclick="document.getElementById('fotoMain').src=this.src; openPhotoModal(this.src)"
                     style="cursor:pointer;transition:all 0.2s">
              @endforeach
            </div>
          @endif
        @else
          <div class="foto-placeholder">📦</div>
        @endif
      </div>
    </div>

    <!-- KANAN: Detail & Aksi -->
    <div class="detail-right">
      <div class="pcard-cat" style="margin-bottom:6px">{{ $produk->kategori->nama_kategori }}</div>
      <h1 class="detail-nama">{{ $produk->nama_produk }}</h1>

      <div class="detail-badges">
        <span class="badge {{ $produk->kondisi === 'Baru' ? 'b-baru' : 'b-bekas' }}">{{ $produk->kondisi }}</span>
        <span class="badge {{ $produk->status === 'tersedia' ? 'b-tersedia' : 'b-habis' }}">
          {{ $produk->status === 'tersedia' ? 'Tersedia' : 'Habis' }}
        </span>
      </div>

      <div class="detail-harga">{{ $produk->formatHarga() }}</div>

      @if($produk->deskripsi)
        <p class="detail-desc">{!! nl2br(e($produk->deskripsi)) !!}</p>
      @endif

      <!-- PENJUAL -->
      <a href="{{ route('seller.profile', $produk->penjual->id_user) }}"
         style="text-decoration:none;color:inherit;display:block">
        <div class="seller-card" style="cursor:pointer;transition:all .2s"
             onmouseover="this.style.boxShadow='0 4px 18px rgba(0,0,0,0.1)'"
             onmouseout="this.style.boxShadow=''">
          <div class="seller-avatar">{{ strtoupper(substr($produk->penjual->nama, 0, 2)) }}</div>
          <div>
            <strong>{{ $produk->penjual->nama }}</strong>
            <div style="font-size:11px;color:var(--accent);font-weight:600;margin-top:1px">Lihat profil & ulasan →</div>
            <div style="font-size:12px;color:var(--text2)">📍 {{ $produk->penjual->lokasi ?? '-' }}</div>
            @php
              $sellerAvg   = $produk->penjual->avgSellerRating();
              $sellerCount = $produk->penjual->sellerReviewsReceived->count();
            @endphp
            <div style="font-size:12px;color:#EF9F27">
              ⭐ {{ $sellerAvg }} ({{ $sellerCount }} ulasan)
            </div>
          </div>
        </div>
      </a>

      <!-- TOMBOL BELI -->
      @if($produk->status === 'tersedia')
        @auth
          @if(auth()->user()->id_user !== $produk->penjual->id_user && !auth()->user()->isAdmin())
            <a class="btn btn-primary btn-full" href="{{ route('checkout', $produk->id_produk) }}">🛒 Pesan &amp; Atur COD</a>
          @elseif(auth()->user()->id_user === $produk->penjual->id_user)
            <a class="btn btn-ghost btn-full" href="{{ route('penjual.produk.edit', $produk->id_produk) }}">✏️ Edit Produk Saya</a>
          @endif
        @else
          <a class="btn btn-primary btn-full" href="{{ route('login') }}">Login untuk Membeli</a>
        @endauth
      @else
        <button class="btn btn-primary btn-full" disabled>Stok Habis</button>
      @endif

    </div><!-- /detail-right -->
  </div><!-- /detail-grid -->

  <!-- REVIEW SECTION -->
  <div class="review-section">
    <h2 class="review-title" style="font-size:18px;font-weight:700;margin-bottom:1rem">
      Ulasan Pembeli ({{ $produk->review->count() }})
    </h2>

    @if($produk->review->isNotEmpty())
      <div class="review-list">
        @foreach($produk->review as $rv)
          <div class="review-item">
            <div class="review-header" style="display:flex;justify-content:space-between;margin-bottom:4px">
              <strong class="review-name">{{ $rv->user->nama }}</strong>
              <div class="review-stars">{{ str_repeat('★', $rv->rating) }}{{ str_repeat('☆', 5 - $rv->rating) }}</div>
            </div>
            <p class="review-text">{{ $rv->komentar }}</p>
            @if($rv->balasan_penjual)
              <div class="balasan-penjual">
                💬 <strong>Penjual:</strong> {{ $rv->balasan_penjual }}
              </div>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <p style="color:var(--text2);font-size:14px">Belum ada ulasan untuk produk ini.</p>
    @endif

    <!-- FORM ULASAN -->
    @guest
      <div style="background:var(--bg);border-radius:10px;padding:1rem;text-align:center;margin-top:1rem;font-size:14px;color:var(--text2)">
        <a href="{{ route('login') }}" style="color:var(--accent);font-weight:700">Login</a> untuk menulis ulasan.
      </div>
    @else
      @php
        $sudahReview = $produk->review->where('id_user', auth()->id())->isNotEmpty();
        $bolehReview = $produk->status === 'habis';
      @endphp
      @if($sudahReview)
        <div style="background:var(--green-bg);color:var(--green);border-radius:10px;padding:1rem;margin-top:1rem;font-size:14px;font-weight:600">
          ✅ Kamu sudah memberikan ulasan untuk produk ini.
        </div>
      @elseif(!$bolehReview)
        <div style="background:var(--bg);border-radius:10px;padding:1rem;margin-top:1rem;font-size:14px;color:var(--text2)">
          💬 Ulasan hanya bisa ditulis setelah transaksi <strong>Selesai</strong>.
        </div>
      @else
        <div class="review-form-box">
          <h3 style="font-size:16px;font-weight:700;margin-bottom:1rem">⭐ Tulis Ulasan</h3>
          <form method="POST" action="{{ route('review.store', $produk->id_produk) }}">
            @csrf
            <div class="form-group">
              <label class="form-label">Rating</label>
              <div class="star-select">
                @for($i = 5; $i >= 1; $i--)
                  <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                  <label for="star{{ $i }}">★</label>
                @endfor
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Komentar</label>
              <textarea class="form-control" name="komentar" rows="3"
                        placeholder="Ceritakan pengalaman transaksimu..."></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Kirim Ulasan</button>
          </form>
        </div>
      @endif
    @endguest
  </div>

</div><!-- /detail-wrap -->

<!-- PHOTO MODAL GALLERY -->
<div id="photoModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:10000;align-items:center;justify-content:center;">
  <div style="position:relative;width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;">
    <img id="photoModalImg" src="" alt="" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:8px;box-shadow:0 10px 40px rgba(0,0,0,0.3);">
    
    <!-- Info & Controls -->
    <div style="margin-top:20px;display:flex;gap:20px;align-items:center;justify-content:center;color:white;">
      <button id="prevPhotoBtn" onclick="previousPhoto()" style="background:rgba(255,255,255,0.2);border:none;color:white;width:40px;height:40px;border-radius:50%;font-size:20px;cursor:pointer;transition:all 0.2s;" title="Foto Sebelumnya">❮</button>
      <span id="photoCounter" style="font-size:14px;min-width:60px;text-align:center;font-weight:600;"></span>
      <button id="nextPhotoBtn" onclick="nextPhoto()" style="background:rgba(255,255,255,0.2);border:none;color:white;width:40px;height:40px;border-radius:50%;font-size:20px;cursor:pointer;transition:all 0.2s;" title="Foto Berikutnya">❯</button>
    </div>
    
    <!-- Close Button -->
    <button onclick="closePhotoModal()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.2);border:none;color:white;font-size:28px;cursor:pointer;font-weight:700;width:40px;height:40px;border-radius:50%;transition:all 0.2s;" title="Tutup">✕</button>
  </div>
</div>

@php
$photoUrls = [];
foreach($produk->foto as $foto) {
  $photoUrls[] = asset('storage/produk/' . $foto->foto);
}
@endphp

<script>
let currentPhotoIndex = 0;

let photos = JSON.parse('@json($photoUrls)');

function openPhotoModal(src) {
  if (!photos || photos.length === 0 || !src) return;
  currentPhotoIndex = photos.indexOf(src);
  if (currentPhotoIndex === -1) currentPhotoIndex = 0;
  updatePhotoModal();
  const modal = document.getElementById('photoModal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
  const modal = document.getElementById('photoModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

function updatePhotoModal() {
  if (!photos || photos.length === 0) return;
  const img = document.getElementById('photoModalImg');
  const counter = document.getElementById('photoCounter');
  const prevBtn = document.getElementById('prevPhotoBtn');
  const nextBtn = document.getElementById('nextPhotoBtn');
  
  img.src = photos[currentPhotoIndex];
  counter.textContent = (currentPhotoIndex + 1) + ' / ' + photos.length;
  
  prevBtn.style.display = currentPhotoIndex > 0 ? 'block' : 'none';
  nextBtn.style.display = currentPhotoIndex < photos.length - 1 ? 'block' : 'none';
}

function previousPhoto() {
  if (!photos || photos.length === 0) return;
  if (currentPhotoIndex > 0) {
    currentPhotoIndex--;
    updatePhotoModal();
  }
}

function nextPhoto() {
  if (!photos || photos.length === 0) return;
  if (currentPhotoIndex < photos.length - 1) {
    currentPhotoIndex++;
    updatePhotoModal();
  }
}

// Close modal when clicking outside image
const photoModal = document.getElementById('photoModal');
if (photoModal) {
  photoModal.addEventListener('click', function(e) {
    if (e.target === this) {
      closePhotoModal();
    }
  });
}

// Navigation with keyboard
document.addEventListener('keydown', function(e) {
  const modal = document.getElementById('photoModal');
  if (modal && modal.style.display === 'flex') {
    if (e.key === 'Escape') closePhotoModal();
    if (e.key === 'ArrowLeft') previousPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
  }
});
</script>

@endsection
