@extends('layouts.app')
@section('title', 'Daftar Penjual — SecondLife')
@section('content')

<div class="form-page-wrap">
  <div class="form-page-box">
    @if(Auth::check())
      <h2 class="form-page-title">🏪 Upgrade Ke Penjual</h2>
      <p style="color:var(--text2);font-size:14px;margin-bottom:1.5rem">
        Selamat datang, <strong>{{ Auth::user()->nama }}</strong>! Upload foto KTM untuk diverifikasi admin, maka kamu bisa langsung berjualan.
      </p>
    @else
      <h2 class="form-page-title">🏪 Daftar Sebagai Penjual</h2>
      <p style="color:var(--text2);font-size:14px;margin-bottom:1.5rem">
        Isi form di bawah dan upload foto KTM untuk diverifikasi admin.
        Setelah disetujui, kamu bisa langsung berjualan!
      </p>
    @endif

    <!-- STEP INDICATOR -->
    <div class="step-indicator">
      <div class="step @if(Auth::check()) active @endif"><span>1</span> Data Diri</div>
      <div class="step-line"></div>
      <div class="step active"><span>2</span> Upload KTM</div>
      <div class="step-line"></div>
      <div class="step"><span>3</span> Verifikasi Admin</div>
    </div>

    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('daftar.penjual') }}" enctype="multipart/form-data">
      @csrf

      @if(!Auth::check())
        <div class="form-section-title">📋 Data Akun</div>
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="req">*</span></label>
          <input class="form-control" type="text" name="nama" value="{{ old('nama') }}" placeholder="Sesuai KTM" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email <span class="req">*</span></label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@mhs.ac.id" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password <span class="req">*</span></label>
            <input class="form-control" type="password" name="password" placeholder="Min 6 karakter" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi Password <span class="req">*</span></label>
          <input class="form-control" type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>
      @endif

      <div class="form-section-title" style="margin-top:1rem">📋 Data Kontak</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">No. WhatsApp @if(!Auth::check())<span class="req">*</span>@endif</label>
          <input class="form-control" type="text" name="no_hp" value="{{ old('no_hp', Auth::check() ? Auth::user()->no_hp : '') }}" placeholder="08xxxxxxxxxx" @if(!Auth::check()) required @endif>
          <small style="color:var(--text2);font-size:11px">📱 Pembeli akan chat via WA untuk atur COD</small>
        </div>
        <div class="form-group">
          <label class="form-label">Lokasi / Kampus</label>
          <input class="form-control" type="text" name="lokasi" value="{{ old('lokasi', Auth::check() ? Auth::user()->lokasi : '') }}" placeholder="Contoh: UGM Yogyakarta">
        </div>
      </div>

      <div class="form-section-title" style="margin-top:1.5rem">📄 Upload KTM</div>
      <div class="form-group">
        <label class="form-label">Foto Kartu Tanda Mahasiswa (KTM) <span class="req">*</span></label>
        <div class="ktm-upload-area" id="ktmArea" onclick="document.getElementById('ktmInput').click()">
          <input type="file" id="ktmInput" name="foto_ktm" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                 onchange="previewKtm(this)" style="display:none" required>
          <div id="ktmPreview">
            <div style="font-size:48px">🪪</div>
            <div style="font-weight:600;margin:.5rem 0">Klik untuk upload foto KTM</div>
            <div style="font-size:13px;color:var(--text2)">Format: JPG, PNG · Maks 2MB</div>
          </div>
        </div>
        <div class="ktm-tips">
          <strong>💡 Tips foto KTM yang baik:</strong>
          <ul>
            <li>Pastikan nama, NIM, dan foto wajah terlihat jelas</li>
            <li>Foto dalam kondisi terang, tidak buram</li>
            <li>Jangan difoto miring atau terpotong</li>
          </ul>
        </div>
      </div>

      <button class="btn btn-primary btn-full" type="submit" style="margin-top:1rem">
        🚀 @if(Auth::check())Upgrade Ke Penjual @else Daftar Sebagai Penjual @endif
      </button>
      @if(!Auth::check())
        <p style="text-align:center;margin-top:.75rem;font-size:13px;color:var(--text2)">
          Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600">Masuk di sini</a>
        </p>
      @else
        <p style="text-align:center;margin-top:.75rem;font-size:13px;color:var(--text2)">
          <a href="{{ route('home') }}" style="color:var(--accent);font-weight:600">← Kembali ke Beranda</a>
        </p>
      @endif
    </form>
  </div>
</div>

@push('scripts')
<script>
function previewKtm(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('ktmPreview').innerHTML =
        `<img src="${e.target.result}" style="max-height:200px;border-radius:8px;margin:0 auto">
         <div style="margin-top:.5rem;font-size:13px;color:var(--text2)">${file.name}</div>`;
    };
    reader.readAsDataURL(file);
  }
}
</script>
@endpush

@endsection
