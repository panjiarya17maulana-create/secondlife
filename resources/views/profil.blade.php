@extends('layouts.app')
@section('title', 'Profil Saya — SecondLife')
@section('content')

<div class="page-section">

  <!-- PROFIL HEADER -->
  <div class="profile-header">
    <div class="profile-avatar">{{ strtoupper(substr($user->nama, 0, 2)) }}</div>
    <div style="flex:1">
      <h2 style="font-family:var(--font-display);font-size:22px;font-weight:800">{{ $user->nama }}</h2>
      <p style="font-size:14px;color:var(--text2);margin-top:4px">
        {{ $user->email }} · 📍{{ $user->lokasi ?? '-' }}
      </p>
    </div>
    @if($user->isPenjual())
      <a class="btn btn-primary" href="{{ route('penjual.produk.tambah') }}">+ Jual Barang</a>
    @endif
  </div>

  <!-- TABS -->
  <div class="tabs">
    <a class="tab {{ request('tab','edit') === 'edit' ? 'active' : '' }}" href="{{ route('profil', ['tab'=>'edit']) }}">Edit Profil</a>
    <a class="tab {{ request('tab') === 'password' ? 'active' : '' }}" href="{{ route('profil', ['tab'=>'password']) }}">Ganti Password</a>
    @if($user->isPenjual())
      <a class="tab {{ request('tab') === 'reputasi' ? 'active' : '' }}" href="{{ route('profil', ['tab'=>'reputasi']) }}"> Review</a>
    @endif
    @if(!$user->isPenjual())
      <a class="tab" href="{{ route('daftar.penjual') }}" style="color:var(--accent);font-weight:700"> Jadi Penjual →</a>
    @endif
    <a class="tab {{ request('tab') === 'hapus' ? 'active' : '' }}" href="{{ route('profil', ['tab'=>'hapus']) }}" style="color:var(--red);font-weight:600">Hapus Akun</a>
  </div>

  @if($errors->any())
    <div class="alert alert-error" style="margin-top:1rem">{{ $errors->first() }}</div>
  @endif

  @if(request('tab') === 'password')
    <!-- GANTI PASSWORD -->
    <div style="max-width:560px;margin-top:1.5rem">
      <div style="background:#F0F7FF;border:2px solid #BCDDFE;border-radius:16px;padding:24px 28px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
          <div style="width:48px;height:48px;background:#E6F1FB;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">🔑</div>
          <div>
            <h3 style="font-size:18px;font-weight:800;color:var(--blue);margin:0">Keamanan Akun</h3>
            <p style="font-size:13px;color:#185FA5;margin:2px 0 0">Perbarui password Anda secara berkala</p>
          </div>
        </div>

        <div style="background:white;border-radius:12px;padding:16px 18px;margin-bottom:18px;border:1px solid #D0E7FF">
          <p style="font-size:14px;color:var(--text);margin:0;font-weight:600">Gunakan kombinasi password yang kuat untuk menjaga keamanan akun Anda.</p>
        </div>

        <form id="formGantiPassword" method="POST" action="{{ route('profil.update') }}">
          @csrf
          <input type="hidden" name="tab" value="password">
          <div class="form-group" style="margin-bottom:16px">
            <label class="form-label" style="color:#185FA5;font-weight:700">Password Baru</label>
            <input class="form-control" type="password" name="password" placeholder="Min 6 karakter" required
                   style="border-color:#BCDDFE;background:#FAFCFF">
          </div>
          <div class="form-group" style="margin-bottom:16px">
            <label class="form-label" style="color:#185FA5;font-weight:700">Konfirmasi Password Baru</label>
            <input class="form-control" type="password" name="password_confirmation" placeholder="Ulangi password baru" required
                   style="border-color:#BCDDFE;background:#FAFCFF">
          </div>
          <button type="button" onclick="showModal('modalPassword')" style="width:100%;padding:14px;background:var(--blue);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s"
                  onmouseover="this.style.background='#104f8a'" onmouseout="this.style.background='var(--blue)'">
             Simpan Password
          </button>
        </form>
      </div>
    </div>

  @elseif(request('tab') === 'hapus')
    <!-- HAPUS AKUN -->
    <div style="max-width:560px;margin-top:1.5rem">
      <div style="background:#FFF5F5;border:2px solid #FCC;border-radius:16px;padding:24px 28px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
          <div style="width:48px;height:48px;background:#FCEBEB;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">⚠️</div>
          <div>
            <h3 style="font-size:18px;font-weight:800;color:var(--red);margin:0">Zona Berbahaya</h3>
            <p style="font-size:13px;color:#A32D2D;margin:2px 0 0">Tindakan ini tidak dapat dibatalkan</p>
          </div>
        </div>

        <div style="background:white;border-radius:12px;padding:16px 18px;margin-bottom:18px;border:1px solid #FDD">
          <p style="font-size:14px;color:var(--text);margin:0 0 10px;font-weight:600">Dengan menghapus akun, semua data akan dihapus secara permanen.</p>
        </div>

        <form id="formHapusAkun" method="POST" action="{{ route('profil.destroy') }}">
          @csrf
          @method('DELETE')
          <div class="form-group" style="margin-bottom:16px">
            <label class="form-label" style="color:#A32D2D;font-weight:700">Masukkan password Anda untuk konfirmasi</label>
            <input class="form-control" type="password" name="password_konfirmasi" placeholder="Ketik password Anda..." required
                   style="border-color:#FCC;background:#FFFAFA">
          </div>
          <button type="button" onclick="showModal('modalHapus')" style="width:100%;padding:14px;background:var(--red);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s"
                  onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='var(--red)'">
             Hapus Akun
          </button>
        </form>
      </div>
    </div>

  @elseif(request('tab') === 'reputasi')
    <!-- REPUTASI PENJUAL -->
    @php
      $reviews     = $user->sellerReviewsReceived()->with('reviewer')->latest()->get();
      $avgRating   = $user->avgSellerRating();
      $totalReview = $reviews->count();
    @endphp
    <div style="max-width:640px;margin-top:1.5rem">

      {{-- Summary Card --}}
      <div style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:2px solid #FDE68A;border-radius:16px;padding:24px;display:flex;align-items:center;gap:20px;margin-bottom:1.5rem">
        <div style="text-align:center;flex-shrink:0">
          <div style="font-size:48px;font-weight:900;color:#D97706;line-height:1">{{ $avgRating }}</div>
          <div style="font-size:20px;color:#F59E0B;margin:4px 0">
            @for($i = 1; $i <= 5; $i++)
              <span style="color:{{ $i <= round((float)$avgRating) ? '#F59E0B' : '#D1D5DB' }}">★</span>
            @endfor
          </div>
          <div style="font-size:12px;color:#92400E;font-weight:600">{{ $totalReview }} ulasan</div>
        </div>
        <div>
          <h3 style="font-size:17px;font-weight:800;color:#92400E;margin:0 0 6px">Reputasi Penjual Anda</h3>
          <p style="font-size:13px;color:#78350F;margin:0;line-height:1.6">
            Rating ini dikumpulkan dari ulasan pembeli setelah transaksi selesai.
            Semakin tinggi rating, semakin dipercaya oleh calon pembeli.
          </p>
        </div>
      </div>

      {{-- Daftar Review --}}
      @if($reviews->isEmpty())
        <div style="background:var(--bg);border:2px dashed var(--border);border-radius:14px;padding:32px;text-align:center;color:var(--text2)">
          <div style="font-size:40px;margin-bottom:10px">📭</div>
          <p style="font-size:14px;font-weight:600">Belum ada ulasan</p>
          <p style="font-size:13px;margin:0">Ulasan akan muncul setelah pembeli menyelesaikan transaksi dengan Anda.</p>
        </div>
      @else
        <div style="display:flex;flex-direction:column;gap:12px">
          @foreach($reviews as $rv)
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:16px">
              <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#FF8C42);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0">
                    {{ strtoupper(substr($rv->reviewer->nama ?? '?', 0, 2)) }}
                  </div>
                  <div>
                    <strong style="font-size:14px">{{ $rv->reviewer->nama ?? 'Anonim' }}</strong>
                    <div style="font-size:11px;color:var(--text2)">{{ $rv->created_at->format('d M Y') }}</div>
                  </div>
                </div>
                <div style="font-size:16px;color:#F59E0B;flex-shrink:0">
                  @for($i = 1; $i <= 5; $i++)
                    <span style="color:{{ $i <= $rv->rating ? '#F59E0B' : '#D1D5DB' }}">★</span>
                  @endfor
                </div>
              </div>
              @if($rv->comment)
                <p style="font-size:13px;color:var(--text);margin:0;padding:10px;background:var(--bg);border-radius:8px;line-height:1.6">
                  "{{ $rv->comment }}"
                </p>
              @else
                <p style="font-size:12px;color:var(--text2);margin:0;font-style:italic">Tidak ada komentar</p>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>

  @else
    <!-- EDIT PROFIL -->
    <div style="max-width:560px;margin-top:1.5rem">
      <div style="background:#F4F8F0;border:2px solid #D2E4BC;border-radius:16px;padding:24px 28px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
          <div style="width:48px;height:48px;background:#EAF3DE;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">👤</div>
          <div>
            <h3 style="font-size:18px;font-weight:800;color:var(--green);margin:0">Ubah Profil</h3>
            <p style="font-size:13px;color:#3B6D11;margin:2px 0 0">Perbarui informasi kontak dan data diri Anda</p>
          </div>
        </div>

        <div style="background:white;border-radius:12px;padding:16px 18px;margin-bottom:18px;border:1px solid #E2ECD6">
          <p style="font-size:14px;color:var(--text);margin:0;font-weight:600">Pastikan data Anda sudah benar untuk mempermudah komunikasi dengan pembeli atau penjual.</p>
        </div>

        <form id="formEditProfil" method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group" style="margin-bottom:16px">
            <label class="form-label" style="color:#3B6D11;font-weight:700">Nama Lengkap</label>
            <input class="form-control" type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                   style="border-color:#D2E4BC;background:#F9FCF7">
          </div>
          <div class="form-group" style="margin-bottom:16px">
            <label class="form-label" style="color:#3B6D11;font-weight:700">Email (Tidak dapat diubah)</label>
            <input class="form-control" type="email" value="{{ $user->email }}" disabled 
                   style="background:#f5f5f5;cursor:not-allowed;border-color:var(--border)">
          </div>
          <div class="form-row">
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label" style="color:#3B6D11;font-weight:700">No. WhatsApp</label>
              <input class="form-control" type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx"
                     style="border-color:#D2E4BC;background:#F9FCF7">
            </div>
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label" style="color:#3B6D11;font-weight:700">Lokasi / Kampus</label>
              <input class="form-control" type="text" name="lokasi" value="{{ old('lokasi', $user->lokasi) }}" placeholder="UGM Yogyakarta"
                     style="border-color:#D2E4BC;background:#F9FCF7">
            </div>
          </div>
          @if($user->isPenjual())
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label" style="color:#3B6D11;font-weight:700">Update Foto KTM</label>
              @if($user->foto_ktm)
                <img src="{{ asset('storage/ktm/'.$user->foto_ktm) }}" style="width:140px;height:100px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;border:1px solid #D2E4BC">
              @endif
              <input class="form-control" type="file" name="foto_ktm" accept="image/*"
                     style="border-color:#D2E4BC;background:#F9FCF7">
            </div>
          @endif
          <button type="button" onclick="showModal('modalEdit')" style="width:100%;padding:14px;background:var(--green);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s"
                  onmouseover="this.style.background='#2c520c'" onmouseout="this.style.background='var(--green)'">
             Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  @endif

</div>


<div id="modalEdit" class="confirm-modal-overlay" onclick="if(event.target===this) hideModal('modalEdit')">
  <div class="confirm-modal">
    <div class="confirm-modal-icon confirm-modal-icon--edit">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
      </svg>
    </div>
    <h3 class="confirm-modal-title">Simpan Perubahan Profil?</h3>
    <p class="confirm-modal-desc">Data profil Anda akan diperbarui dengan informasi yang baru dimasukkan. Pastikan semua data sudah benar.</p>
    <div class="confirm-modal-actions">
      <button type="button" class="confirm-modal-btn confirm-modal-btn--cancel" onclick="hideModal('modalEdit')">Batal</button>
      <button type="button" class="confirm-modal-btn confirm-modal-btn--confirm" onclick="document.getElementById('formEditProfil').submit()">Ya, Simpan</button>
    </div>
  </div>
</div>

<div id="modalPassword" class="confirm-modal-overlay" onclick="if(event.target===this) hideModal('modalPassword')">
  <div class="confirm-modal">
    <div class="confirm-modal-icon" style="background: linear-gradient(135deg, #e6f1fb, #bcddfe); color: #185fa5;">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
    </div>
    <h3 class="confirm-modal-title">Ganti Password Anda?</h3>
    <p class="confirm-modal-desc">Apakah Anda yakin ingin memperbarui password? Anda harus menggunakan password baru ini untuk masuk ke akun Anda selanjutnya.</p>
    <div class="confirm-modal-actions">
      <button type="button" class="confirm-modal-btn confirm-modal-btn--cancel" onclick="hideModal('modalPassword')">Batal</button>
      <button type="button" class="confirm-modal-btn confirm-modal-btn--confirm" style="background: linear-gradient(135deg, #2b78c5, #185fa5); box-shadow: 0 4px 14px rgba(24,95,165,0.3);" onclick="document.getElementById('formGantiPassword').submit()">Ya, Ganti</button>
    </div>
  </div>
</div>


<div id="modalHapus" class="confirm-modal-overlay" onclick="if(event.target===this) hideModal('modalHapus')">
  <div class="confirm-modal">
    <div class="confirm-modal-icon confirm-modal-icon--danger">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/>
        <line x1="12" y1="17" x2="12.01" y2="17"/>
      </svg>
    </div>
    <h3 class="confirm-modal-title" style="color:#e74c3c">Peringatan Terakhir!</h3>
    <p class="confirm-modal-desc">Anda akan menghapus akun secara <strong>PERMANEN</strong>. Semua data akan hilang dan <strong>TIDAK BISA</strong> dikembalikan. Apakah Anda yakin ingin melanjutkan?</p>
    <div class="confirm-modal-actions">
      <button type="button" class="confirm-modal-btn confirm-modal-btn--cancel" onclick="hideModal('modalHapus')">Batal, Kembali</button>
      <button type="button" class="confirm-modal-btn confirm-modal-btn--danger" onclick="document.getElementById('formHapusAkun').submit()">Ya, Hapus Akun</button>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/confirm-modal.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/confirm-modal.js') }}"></script>
@endpush

@endsection
