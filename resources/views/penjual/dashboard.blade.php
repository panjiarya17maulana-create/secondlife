@extends('layouts.app')
@section('title', 'Dashboard Penjual — SecondLife')
@section('content')

<div class="page-section" style="max-width:1100px">

  <!-- HEADER -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800;margin:0">🏪 Dashboard Penjual</h2>
      <p style="color:var(--text2);font-size:14px;margin:4px 0 0">Halo, <strong>{{ auth()->user()->nama }}</strong>!</p>
    </div>
    <a class="btn btn-primary" href="{{ route('penjual.produk.tambah') }}">+ Tambah Produk</a>
  </div>

  <!-- STATS -->
  <div class="dashboard-grid" style="margin-bottom:2rem">
    <div class="dash-card">
      <div class="dash-label">💰 Total Pendapatan</div>
      <div class="dash-value" style="color:var(--accent)">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
    </div>
    <div class="dash-card">
      <div class="dash-label">📦 Produk Aktif</div>
      <div class="dash-value">{{ $produkList->where('status','tersedia')->count() }}</div>
    </div>
    <div class="dash-card">
      <div class="dash-label">🎉 Trx Selesai</div>
      <div class="dash-value">{{ $statTransaksi['selesai'] ?? 0 }}</div>
    </div>
    <div class="dash-card">
      <div class="dash-label">⏳ Perlu Diproses</div>
      <div class="dash-value {{ ($statTransaksi['menunggu_konfirmasi'] ?? 0) > 0 ? 'text-danger' : '' }}">
        {{ $statTransaksi['menunggu_konfirmasi'] ?? 0 }}
      </div>
    </div>
    <div class="dash-card">
      <div class="dash-label">⭐ Rating Rata-rata</div>
      <div class="dash-value">⭐ {{ $reviewList->avg('rating') ? round($reviewList->avg('rating'),1) : '-' }}
        <span style="font-size:13px;color:var(--text2)">({{ $reviewList->count() }})</span>
      </div>
    </div>
    <div class="dash-card">
      <div class="dash-label">💬 Review Belum Dibalas</div>
      @php $belumBalas = $reviewList->filter(fn($r) => !$r->balasan_penjual)->count(); @endphp
      <div class="dash-value {{ $belumBalas > 0 ? 'text-danger' : '' }}">
      {{ $belumBalas }}
</div>
    </div>
  </div>

  <!-- TABS -->
  <div class="tabs" style="margin-bottom:1.5rem">
    <a class="tab {{ $tab==='produk'?'active':'' }}" href="{{ route('penjual.dashboard', ['tab'=>'produk']) }}">📦 Produk</a>
    <a class="tab {{ $tab==='transaksi'?'active':'' }}" href="{{ route('penjual.dashboard', ['tab'=>'transaksi']) }}">
      💰 Transaksi
      @if($statTransaksi['menunggu_konfirmasi'] ?? 0)
        <span style="background:var(--red);color:#fff;font-size:10px;padding:1px 6px;border-radius:99px;margin-left:4px">{{ $statTransaksi['menunggu_konfirmasi'] }}</span>
      @endif
    </a>
    <a class="tab {{ $tab==='review'?'active':'' }}" href="{{ route('penjual.dashboard', ['tab'=>'review']) }}">
      ⭐ Review
      @if($belumBalas)
        <span style="background:var(--red);color:#fff;font-size:10px;padding:1px 6px;border-radius:99px;margin-left:4px">{{ $belumBalas }}</span>
      @endif
    </a>
  </div>

  {{-- ── TAB PRODUK ── --}}
  @if($tab === 'produk')
    <p style="font-size:13px;color:var(--text2);margin-bottom:1rem">{{ $produkList->count() }} produk</p>

    @if($produkList->isNotEmpty())
      <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:16px;overflow:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13.5px;min-width:580px">
          <thead>
            <tr style="background:var(--bg);border-bottom:1.5px solid var(--border)">
              <th style="padding:12px 14px;text-align:left;color:var(--text2);font-weight:600">Produk</th>
              <th style="padding:12px 14px;text-align:left;color:var(--text2);font-weight:600">Kategori</th>
              <th style="padding:12px 14px;text-align:right;color:var(--text2);font-weight:600">Harga</th>
              <th style="padding:12px 14px;text-align:center;color:var(--text2);font-weight:600">Status</th>
              <th style="padding:12px 14px;text-align:center;color:var(--text2);font-weight:600">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($produkList as $p)
              <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:12px 14px">
                  <div style="display:flex;align-items:center;gap:10px">
                    @if($p->foto->isNotEmpty())
                      <img src="{{ asset('storage/produk/'.$p->foto->first()->foto) }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0">
                    @else
                      <div style="width:44px;height:44px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">📦</div>
                    @endif
                    <div>
                      <a href="{{ route('produk.detail', $p->id_produk) }}" target="_blank" style="font-weight:600;color:var(--text);text-decoration:none">{{ $p->nama_produk }}</a>
                      <div style="font-size:11px;color:var(--text2)">{{ $p->kondisi }}</div>
                    </div>
                  </div>
                </td>
                <td style="padding:12px 14px"><span style="background:var(--bg);padding:3px 9px;border-radius:20px;font-size:12px">{{ $p->kategori->nama_kategori }}</span></td>
                <td style="padding:12px 14px;text-align:right;font-weight:700;white-space:nowrap">{{ $p->formatHarga() }}</td>
                <td style="padding:12px 14px;text-align:center">
                  @if($p->status === 'tersedia')
                    <span style="background:#EAF3DE;color:#3B6D11;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">✅ Tersedia</span>
                  @elseif($p->status === 'nonaktif')
                    <span style="background:#E8E8E8;color:#666;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">🔒 Nonaktif</span>
                  @else
                    <span style="background:#FCEBEB;color:#A32D2D;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">❌ Habis</span>
                  @endif
                </td>
                <td style="padding:12px 14px;text-align:center">
                  <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap">
                    {{-- Tombol Edit --}}
                    <a href="{{ route('penjual.produk.edit', $p->id_produk) }}" style="background:var(--bg);border:1.5px solid var(--border);color:var(--text);padding:5px 10px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none" title="Edit produk">✏️ Edit</a>

                    {{-- Tombol Sembunyikan / Publikasikan (toggle dari beranda, TIDAK hapus dari database) --}}
                    <form method="POST" action="{{ route('penjual.produk.togglePublish', $p->id_produk) }}" style="display:inline">
                      @csrf @method('PATCH')
                      @if($p->status === 'nonaktif')
                        <button style="background:#EAF3DE;border:1.5px solid #b5d99c;color:#3B6D11;padding:5px 10px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer" type="submit" title="Tampilkan kembali di beranda" onclick="return confirm('Publikasikan produk ini kembali ke beranda?')">📢 Publish</button>
                      @else
                        <button style="background:#FFF8E6;border:1.5px solid #f0d68a;color:#8B6914;padding:5px 10px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer" type="submit" title="Sembunyikan dari beranda (tidak dihapus dari database)" onclick="return confirm('Sembunyikan produk dari beranda?\n\nProduk tidak akan dihapus, hanya disembunyikan dari halaman beranda.')">👁️‍🗨️ Sembunyikan</button>
                      @endif
                    </form>

                    {{-- Tombol Hapus Permanen (hapus dari database) --}}
                    <form method="POST" action="{{ route('penjual.produk.hapus', $p->id_produk) }}" onsubmit="return confirm('⚠️ HAPUS PERMANEN!\n\nProduk akan dihapus secara permanen dari database beserta semua fotonya.\n\nAksi ini TIDAK BISA dibatalkan.\n\nLanjutkan?')" style="display:inline">
                      @csrf @method('DELETE')
                      <button style="background:#fff0f0;border:1.5px solid #fcc;color:var(--red);padding:5px 10px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer" type="submit" title="Hapus permanen dari database">🗑️ Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="empty-state">
        <div class="empty-icon">📦</div>
        Belum ada produk.
        <a href="{{ route('penjual.produk.tambah') }}" style="color:var(--accent);font-weight:600;margin-top:8px;display:block">+ Tambah Produk →</a>
      </div>
    @endif

  {{-- ── TAB TRANSAKSI ── --}}
  @elseif($tab === 'transaksi')
    <p style="font-size:13px;color:var(--text2);margin-bottom:1rem">{{ $transaksiList->count() }} transaksi</p>

    @if($transaksiList->isNotEmpty())
      <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($transaksiList as $t)
          @php $dt = $t->detail->first(); $produk = $dt?->produk; @endphp
          <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:14px;padding:16px 20px">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
              <div>
                <div style="font-weight:700;font-size:15px">{{ $produk->nama_produk ?? '-' }}</div>
                <div style="font-size:13px;color:var(--text2);margin-top:3px">
                  Pembeli: <strong>{{ $t->pembeli->nama }}</strong>
                  @if($t->pembeli->no_hp)
                    · <a href="{{ $t->pembeli->waLink('Halo '.$t->pembeli->nama.', saya penjual SecondLife terkait transaksi #'.$t->id_transaksi) }}" target="_blank" style="color:var(--accent)">💬 WA</a>
                  @endif
                </div>
                <div style="font-size:12px;color:var(--text2)">Trx #{{ $t->id_transaksi }}</div>
              </div>
              <div style="text-align:right">
                <div style="font-weight:800;font-size:18px;color:var(--accent)">{{ $t->formatTotal() }}</div>
                @php
                  $stC = match($t->status) {
                    'menunggu_konfirmasi' => 'background:#FAEEDA;color:#BA7517',
                    'dikonfirmasi'        => 'background:#EAF3DE;color:#3B6D11',
                    'selesai'             => 'background:#EAF3DE;color:#3B6D11',
                    'dibatalkan'          => 'background:#FCEBEB;color:#A32D2D',
                    default               => ''
                  };
                @endphp
                <span
                class="status-badge"
                style="background:#dcfce7;color:#166534">
                {{ $t->labelStatus() }}
                </span>
              </div>
            </div>

            @if(!in_array($t->status, ['selesai','dibatalkan']))
              <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <span style="font-size:13px;color:var(--text2);font-weight:500">Update:</span>
                @if($t->status === 'menunggu_konfirmasi')
                  <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}">
                    @csrf
                    <input type="hidden" name="status_baru" value="dikonfirmasi">
                    <button class="btn btn-primary" type="submit" style="padding:7px 16px;font-size:13px" onclick="return confirm('Konfirmasi?')">✅ Konfirmasi</button>
                  </form>
                @endif
                @if($t->status === 'dikonfirmasi')
                  <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}">
                    @csrf
                    <input type="hidden" name="status_baru" value="selesai">
                    <button class="btn btn-primary" type="submit" style="padding:7px 16px;font-size:13px" onclick="return confirm('Selesaikan?')">🎉 Selesaikan</button>
                  </form>
                @endif
                <form method="POST" action="{{ route('penjual.transaksi.aksi', $t->id_transaksi) }}">
                  @csrf
                  <input type="hidden" name="status_baru" value="dibatalkan">
                  <button style="background:none;border:1.5px solid var(--red);color:var(--red);padding:7px 16px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer" type="submit" onclick="return confirm('Batalkan? Stok dikembalikan.')">❌ Batalkan</button>
                </form>
                <a href="{{ route('transaksi.detail', $t->id_transaksi) }}" style="color:var(--text2);font-size:13px;text-decoration:none">🔍 Detail →</a>
              </div>
            @else
              <div style="margin-top:10px">
                <a href="{{ route('transaksi.detail', $t->id_transaksi) }}" style="color:var(--text2);font-size:13px;text-decoration:none">🔍 Lihat Detail →</a>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="empty-state"><div class="empty-icon">📭</div>Tidak ada transaksi ditemukan.</div>
    @endif

  {{-- ── TAB REVIEW ── --}}
  @elseif($tab === 'review')
    <div style="display:flex;gap:10px;margin-bottom:1.25rem;flex-wrap:wrap">
      <a href="{{ route('penjual.dashboard', ['tab'=>'review']) }}" class="btn {{ !request('filter_review') ? 'btn-primary' : 'btn-ghost' }}" style="padding:8px 16px;font-size:13px">Semua ({{ $reviewList->count() }})</a>
      <a href="{{ route('penjual.dashboard', ['tab'=>'review','filter_review'=>'belum_dibalas']) }}" class="btn {{ request('filter_review')==='belum_dibalas' ? 'btn-primary' : 'btn-ghost' }}" style="padding:8px 16px;font-size:13px">Belum Dibalas {{ $belumBalas ? "($belumBalas)" : '' }}</a>
    </div>

    @php
      $displayReview = request('filter_review') === 'belum_dibalas'
        ? $reviewList->filter(fn($r) => !$r->balasan_penjual)
        : $reviewList;
    @endphp

    @if($displayReview->isNotEmpty())
      <div style="display:flex;flex-direction:column;gap:12px">
        @foreach($displayReview as $rv)
          <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:14px;padding:16px 20px">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px">
              <div>
                <div style="font-weight:700">{{ $rv->user->nama }}</div>
                <div style="color:#EF9F27;font-size:15px">{{ str_repeat('★', $rv->rating) }}{{ str_repeat('☆', 5-$rv->rating) }}</div>
                <div style="font-size:12px;color:var(--text2)">Produk: {{ $rv->produk->nama_produk ?? '-' }}</div>
              </div>
            </div>
            <p style="margin:10px 0;font-size:14px">{{ $rv->komentar }}</p>

            @if($rv->balasan_penjual)
              <div style="background:var(--white);border-left:3px solid var(--accent);padding:10px 14px;border-radius:0 8px 8px 0;font-size:13px">
                💬 <strong>Balasanmu:</strong> {{ $rv->balasan_penjual }}
              </div>
            @else
              <details>
                <summary style="cursor:pointer;color:var(--accent);font-size:13px;font-weight:600;list-style:none;padding:8px 0">💬 Balas review ini ▾</summary>
                <form method="POST" action="{{ route('penjual.review.balas', $rv->id_review) }}" style="margin-top:8px">
                  @csrf
                  <textarea class="form-control" name="balasan" rows="2" required placeholder="Tulis balasan kamu..."></textarea>
                  <button class="btn btn-primary" type="submit" style="margin-top:8px;padding:7px 16px;font-size:13px">📨 Kirim Balasan</button>
                </form>
              </details>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="empty-state"><div class="empty-icon">⭐</div>Belum ada review untuk produkmu.</div>
    @endif
  @endif

</div>

@endsection
