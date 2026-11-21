@extends('layouts.app')
@section('title', 'Ulasan Penjual — SecondLife Admin')
@section('content')

<div class="page-section">

  {{-- HEADER --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">⭐ Ulasan Penjual</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">
        Total <strong>{{ $reviews->total() }}</strong> ulasan terdaftar
      </p>
    </div>
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">
      ← Kembali ke Dashboard
    </a>
  </div>

  {{-- FLASH MESSAGE --}}
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1.5rem">{{ session('success') }}</div>
  @endif

  {{-- FILTER --}}
  <form method="GET" action="{{ route('admin.reviews.index') }}"
        style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap">
    <input class="form-control" type="text" name="cari"
           placeholder="🔍 Cari nama pembeli, penjual, atau komentar..."
           value="{{ request('cari') }}" style="flex:1;min-width:220px">
    <select class="form-control" name="rating" style="min-width:140px">
      <option value="">Semua Rating</option>
      @for($i = 5; $i >= 1; $i--)
        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
          {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} ({{ $i }} bintang)
        </option>
      @endfor
    </select>
    <button class="btn btn-primary" type="submit">Filter</button>
    <a class="btn btn-ghost" href="{{ route('admin.reviews.index') }}">Reset</a>
  </form>

  {{-- TABEL --}}
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>Pembeli (Pengulas)</th>
            <th>Penjual (Diulas)</th>
            <th style="width:110px">Rating</th>
            <th>Komentar</th>
            <th style="width:110px">Tanggal</th>
            <th style="width:80px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reviews as $rv)
            <tr>
              {{-- No --}}
              <td style="color:var(--text2);font-size:12px">
                {{ ($reviews->currentPage() - 1) * $reviews->perPage() + $loop->iteration }}
              </td>

              {{-- Pembeli --}}
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6366F1,#8B5CF6);
                              display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0">
                    {{ strtoupper(substr($rv->reviewer->nama ?? '?', 0, 2)) }}
                  </div>
                  <div>
                    <div style="font-weight:600;font-size:14px">{{ $rv->reviewer->nama ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $rv->reviewer->email ?? '' }}</div>
                  </div>
                </div>
              </td>

              {{-- Penjual --}}
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#FF8C42);
                              display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0">
                    {{ strtoupper(substr($rv->seller->nama ?? '?', 0, 2)) }}
                  </div>
                  <div>
                    <div style="font-weight:600;font-size:14px">{{ $rv->seller->nama ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $rv->seller->email ?? '' }}</div>
                  </div>
                </div>
              </td>

              {{-- Rating --}}
              <td>
                <div style="font-size:17px;line-height:1">
                  @for($i = 1; $i <= 5; $i++)
                    <span style="color:{{ $i <= $rv->rating ? '#F59E0B' : '#D1D5DB' }}">★</span>
                  @endfor
                </div>
                <div style="font-size:11px;color:var(--text2);margin-top:3px">{{ $rv->rating }}/5</div>
              </td>

              {{-- Komentar --}}
              <td style="max-width:280px">
                @if($rv->comment)
                  <p style="font-size:13px;color:var(--text);margin:0;line-height:1.5;
                             white-space:normal;word-break:break-word">
                    "{{ \Illuminate\Support\Str::limit($rv->comment, 120) }}"
                  </p>
                @else
                  <span style="font-size:12px;color:var(--text2);font-style:italic">Tidak ada komentar</span>
                @endif
              </td>

              {{-- Tanggal --}}
              <td style="font-size:12px;color:var(--text2)">
                {{ $rv->created_at->format('d M Y') }}<br>
                <span style="font-size:11px">{{ $rv->created_at->format('H:i') }}</span>
              </td>

              {{-- Aksi --}}
              <td>
                <form method="POST"
                      action="{{ route('admin.reviews.destroy', $rv->id) }}"
                      onsubmit="return confirm('Hapus ulasan ini? Tindakan tidak dapat dibatalkan.')">
                  @csrf
                  @method('DELETE')
                  <button class="btn-sm" type="submit"
                          style="border-color:var(--red);color:var(--red);white-space:nowrap">
                    🗑️ Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:3rem;color:var(--text2)">
                <div style="font-size:36px;margin-bottom:10px">📭</div>
                <p style="font-weight:600">Tidak ada ulasan ditemukan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- PAGINATION --}}
  <div style="margin-top:1.5rem">
    {{ $reviews->links() }}
  </div>

</div>
@endsection
