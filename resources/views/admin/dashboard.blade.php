@extends('layouts.app')
@section('title', 'Dashboard Admin — SecondLife')
@section('content')

<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <h2>👑 Dashboard Admin</h2>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <a class="btn btn-ghost" href="{{ route('admin.users.index') }}" style="padding:9px 18px;font-size:14px">👥 Kelola User</a>
      <a class="btn btn-ghost" href="{{ route('admin.produk.index') }}" style="padding:9px 18px;font-size:14px">📦 Kelola Produk</a>
      <a class="btn btn-ghost" href="{{ route('admin.kategori.index') }}" style="padding:9px 18px;font-size:14px;position:relative">
        🏷️ Kelola Kategori
        @if($trxMenunggu > 0)
          <span style="position:absolute;top:-6px;right:-6px;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:99px;line-height:1.4">{{ $trxMenunggu }}</span>
        @endif
      </a>
      <a class="btn btn-ghost" href="{{ route('admin.transaksi.index') }}" style="padding:9px 18px;font-size:14px">💰 Semua Transaksi</a>
      <a class="btn btn-ghost" href="{{ route('admin.reviews.index') }}" style="padding:9px 18px;font-size:14px">⭐ Ulasan Penjual</a>
    </div>
  </div>

  <!-- ALERT REQUEST KATEGORI -->
  @if($userMenunggu > 0)
    <div style="background:#FAEEDA;color:#BA7517;border-radius:12px;padding:14px 20px;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
      <span>🪪 Ada <strong>{{ $userMenunggu }}</strong> penjual baru yang perlu diverifikasi.</span>
      <a href="{{ route('admin.verifikasi.index') }}" style="background:#BA7517;color:#fff;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">Verifikasi Sekarang →</a>
    </div>
  @endif

  <!-- STAT CARDS -->
  <div class="dashboard-grid" style="margin-bottom:2rem">
    @foreach([
      ['👤 Total User',     $totalUsers,    ''],
      ['📦 Produk Aktif',   $totalProduk,   ''],
      ['🔄 Total Transaksi', $totalTransaksi,''],
      ['🏷️ Kategori',       $totalKategori, ''],
      ['⏳ Penjual Menunggu',$userMenunggu,  'color:var(--accent)'],
      ['⏳ Trx Menunggu',    $trxMenunggu,   'color:var(--accent)'],
    ] as [$label, $val, $style])
      <div class="dash-card">
        <div class="dash-label">{{ $label }}</div>
        <div class="dash-value" style="{{ $style }}">{{ number_format($val) }}</div>
      </div>
    @endforeach
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;flex-wrap:wrap">

    <!-- MENU CEPAT KIRI -->
    <div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:1rem">⚡ Akses Cepat</h3>
      @foreach([
        ['route'=>'admin.verifikasi.index','icon'=>'🪪','label'=>'Verifikasi Penjual','badge'=>$userMenunggu],
        ['route'=>'admin.users.index','icon'=>'👥','label'=>'Kelola Pengguna','badge'=>0],
        ['route'=>'admin.produk.index','icon'=>'📦','label'=>'Kelola Produk','badge'=>0],
        ['route'=>'admin.transaksi.index','icon'=>'💰','label'=>'Semua Transaksi','badge'=>$trxMenunggu],
        ['route'=>'admin.kategori.index','icon'=>'🏷️','label'=>'Kelola Kategori','badge'=>0],
        ['route'=>'admin.kategori.requests','icon'=>'📨','label'=>'Request Kategori','badge'=>0],
        ['route'=>'admin.reviews.index','icon'=>'⭐','label'=>'Moderasi Ulasan','badge'=>0],
      ] as $m)
        <a class="trx-card" href="{{ route($m['route']) }}" style="display:flex;text-decoration:none;color:inherit;margin-bottom:.5rem">
          <div class="trx-icon" style="font-size:20px">{{ $m['icon'] }}</div>
          <div class="trx-info"><strong>{{ $m['label'] }}</strong></div>
          @if($m['badge'] > 0)
            <span class="badge b-habis">{{ $m['badge'] }}</span>
          @endif
        </a>
      @endforeach
    </div>

    <!-- USER TERBARU -->
    <div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:1rem">👤 Pengguna Terbaru</h3>
      @php $userTerbaru = \App\Models\User::with('roles')->orderByDesc('id_user')->take(8)->get(); @endphp
      @foreach($userTerbaru as $u)
        <div class="trx-card" style="margin-bottom:.5rem">
          <div class="seller-avatar" style="width:40px;height:40px;font-size:13px;flex-shrink:0">
            {{ strtoupper(substr($u->nama, 0, 2)) }}
          </div>
          <div class="trx-info">
            <strong>{{ $u->nama }}</strong>
            <span>{{ $u->email }}</span>
          </div>
          @foreach($u->roles as $r)
            <span class="role-badge role-{{ $r->nama_role }}">{{ $r->nama_role }}</span>
          @endforeach
        </div>
      @endforeach
      <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-full" style="margin-top:8px;font-size:13px">Lihat Semua User →</a>
    </div>

  </div>
</div>

  {{-- ── GRAFIK LAPORAN (Fitur #8) ── --}}
  <div class="page-section" style="margin-top:0;padding-top:0">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:1.25rem">📊 Tren 6 Bulan Terakhir</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

      {{-- Grafik Transaksi --}}
      <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:16px;padding:20px">
        <div style="font-size:14px;font-weight:700;margin-bottom:1rem;color:var(--text)">💰 Jumlah Transaksi</div>
        <canvas id="chartTransaksi" height="120"></canvas>
      </div>

      {{-- Grafik User & Produk --}}
      <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:16px;padding:20px">
        <div style="font-size:14px;font-weight:700;margin-bottom:1rem;color:var(--text)">👥 User & 📦 Produk Baru</div>
        <canvas id="chartUserProduk" height="120"></canvas>
      </div>

    </div>
  </div>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels    = @json($labelBulan);
const trxData   = @json($dataTransaksi);
const userData  = @json($dataUserBaru);
const produkData = @json($dataProdukBaru);

// Warna tema
const accent  = '#6C47FF';
const green   = '#22c55e';
const amber   = '#f59e0b';

// Chart Transaksi
new Chart(document.getElementById('chartTransaksi'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Transaksi',
            data: trxData,
            backgroundColor: accent + '33',
            borderColor: accent,
            borderWidth: 2,
            borderRadius: 8,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});

// Chart User & Produk
new Chart(document.getElementById('chartUserProduk'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'User Baru',
                data: userData,
                borderColor: green,
                backgroundColor: green + '22',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: green,
            },
            {
                label: 'Produk Baru',
                data: produkData,
                borderColor: amber,
                backgroundColor: amber + '22',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: amber,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                labels: { font: { size: 12 } }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
