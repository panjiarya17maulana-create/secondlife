@extends('layouts.app')
@section('title', 'Reputasi ' . $seller->nama . ' — SecondLife')
@section('content')

<style>
.seller-profile-wrap { max-width: 760px; margin: 0 auto; padding: 2rem 1rem; }

.seller-hero {
  background: linear-gradient(135deg, #FFF7ED, #FEF3C7);
  border: 2px solid #FDE68A;
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 2rem;
}

.seller-hero-avatar {
  width: 80px; height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), #FF8C42);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 900; color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(230,100,30,0.3);
}

.seller-hero-info { flex: 1; }
.seller-hero-info h1 { font-size: 22px; font-weight: 800; margin: 0 0 4px; }
.seller-hero-info .lokasi { font-size: 13px; color: var(--text2); margin-bottom: 12px; }

.rating-big { display: flex; align-items: baseline; gap: 6px; }
.rating-big .num { font-size: 42px; font-weight: 900; color: #D97706; line-height: 1; }
.rating-big .stars { font-size: 22px; }
.rating-big .count { font-size: 13px; color: var(--text2); }

/* Distribusi bar */
.rating-dist { margin-top: 1.5rem; }
.rating-dist-row {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 6px; font-size: 13px;
}
.rating-dist-bar-wrap {
  flex: 1; height: 8px; background: var(--border); border-radius: 99px; overflow: hidden;
}
.rating-dist-bar {
  height: 100%;
  background: linear-gradient(90deg, #F59E0B, #FBBF24);
  border-radius: 99px;
  transition: width 0.6s ease;
}
.rating-dist-label { width: 32px; text-align: right; color: var(--text2); font-size: 12px; }

/* Review cards */
.review-list { display: flex; flex-direction: column; gap: 14px; }
.review-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: 16px;
  padding: 18px 20px;
  transition: box-shadow .2s;
}
.review-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }

.review-card-header {
  display: flex; justify-content: space-between;
  align-items: flex-start; margin-bottom: 12px;
}
.reviewer-info { display: flex; align-items: center; gap: 12px; }
.reviewer-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, #6366F1, #8B5CF6);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: white; flex-shrink: 0;
}
.reviewer-name { font-size: 14px; font-weight: 700; }
.reviewer-date { font-size: 11px; color: var(--text2); margin-top: 2px; }

.star-display { font-size: 17px; }
.star-on  { color: #F59E0B; }
.star-off { color: #D1D5DB; }

.review-comment {
  font-size: 13px; color: var(--text);
  background: var(--bg); border-radius: 10px;
  padding: 12px 14px; line-height: 1.65;
}
.no-comment { font-size: 12px; color: var(--text2); font-style: italic; }

.empty-review {
  text-align: center; padding: 48px 24px;
  border: 2px dashed var(--border);
  border-radius: 16px; color: var(--text2);
}
</style>

<div class="seller-profile-wrap">

  {{-- Breadcrumb --}}
  <div class="breadcrumb" style="margin-bottom:1.2rem">
    <a href="{{ route('home') }}">Beranda</a> /
    <span>Reputasi {{ $seller->nama }}</span>
  </div>

  {{-- SELLER HERO CARD --}}
  <div class="seller-hero">
    <div class="seller-hero-avatar">
      {{ strtoupper(substr($seller->nama, 0, 2)) }}
    </div>
    <div class="seller-hero-info">
      <h1>{{ $seller->nama }}</h1>
      <div class="lokasi">📍 {{ $seller->lokasi ?? 'Lokasi tidak diset' }}</div>
      <div class="rating-big">
        <span class="num">{{ $avgRating }}</span>
        <div class="stars">
          @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= round((float)$avgRating) ? 'star-on' : 'star-off' }}">★</span>
          @endfor
        </div>
        <span class="count">{{ $reviews->count() }} ulasan</span>
      </div>
    </div>
  </div>

  {{-- DISTRIBUSI RATING --}}
  @if($reviews->isNotEmpty())
    <div class="rating-dist" style="background:var(--white);border:1.5px solid var(--border);border-radius:16px;padding:20px 24px;margin-bottom:2rem">
      <p style="font-size:14px;font-weight:700;margin:0 0 14px">📊 Distribusi Rating</p>
      @foreach($distribusi as $star => $data)
        <div class="rating-dist-row">
          <span style="font-size:13px;color:#F59E0B;width:24px;text-align:right">{{ $star }}★</span>
          <div class="rating-dist-bar-wrap">
            <div class="rating-dist-bar" style="width:{{ $data['persen'] }}%"></div>
          </div>
          <span class="rating-dist-label">{{ $data['count'] }}</span>
        </div>
      @endforeach
    </div>
  @endif

  {{-- DAFTAR ULASAN --}}
  <p style="font-size:16px;font-weight:700;margin-bottom:1rem">
    💬 Semua Ulasan <span style="font-size:14px;color:var(--text2);font-weight:400">({{ $reviews->count() }})</span>
  </p>

  @if($reviews->isEmpty())
    <div class="empty-review">
      <div style="font-size:48px;margin-bottom:12px">📭</div>
      <p style="font-size:15px;font-weight:600;margin:0 0 6px">Belum ada ulasan</p>
      <p style="font-size:13px;margin:0">Penjual ini belum memiliki ulasan dari pembeli.</p>
    </div>
  @else
    <div class="review-list">
      @foreach($reviews as $rv)
        <div class="review-card">
          <div class="review-card-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar">
                {{ strtoupper(substr($rv->reviewer->nama ?? '?', 0, 2)) }}
              </div>
              <div>
                <div class="reviewer-name">{{ $rv->reviewer->nama ?? 'Anonim' }}</div>
                <div class="reviewer-date">{{ $rv->created_at->diffForHumans() }}</div>
              </div>
            </div>
            <div class="star-display">
              @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $rv->rating ? 'star-on' : 'star-off' }}">★</span>
              @endfor
            </div>
          </div>
          @if($rv->comment)
            <div class="review-comment">"{{ $rv->comment }}"</div>
          @else
            <p class="no-comment">Tidak ada komentar.</p>
          @endif
        </div>
      @endforeach
    </div>
  @endif

</div>

@endsection
