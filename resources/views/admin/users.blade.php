@extends('layouts.app')
@section('title', 'Kelola Pengguna — SecondLife')
@section('content')
<div class="page-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:800">👥 Kelola Pengguna</h2>
      <p style="color:var(--text2);font-size:14px;margin-top:4px">Total {{ $users->total() }} pengguna terdaftar</p>
    </div>
    <div style="display:flex;gap:8px">
      <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" style="padding:9px 18px;font-size:14px">← Kembali</a>
      <a class="btn btn-ghost" href="{{ route('admin.verifikasi.index') }}" style="padding:9px 18px;font-size:14px">🪪 Verifikasi Penjual</a>
    </div>
  </div>

  <!-- FILTER -->
  <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap">
    <input class="form-control" type="text" name="cari" placeholder="🔍 Cari nama atau email..." value="{{ request('cari') }}" style="flex:1;min-width:200px">
    <select class="form-control" name="role" style="min-width:140px">
      <option value="">Semua Role</option>
      <option value="admin"   {{ request('role')==='admin'?'selected':'' }}>Admin</option>
      <option value="penjual" {{ request('role')==='penjual'?'selected':'' }}>Penjual</option>
      <option value="pembeli" {{ request('role')==='pembeli'?'selected':'' }}>Pembeli</option>
    </select>
    <select class="form-control" name="status" style="min-width:140px">
      <option value="">Semua Status</option>
      <option value="aktif"    {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
      <option value="menunggu" {{ request('status')==='menunggu'?'selected':'' }}>Menunggu</option>
      <option value="ditolak"  {{ request('status')==='ditolak'?'selected':'' }}>Ditolak</option>
    </select>
    <button class="btn btn-primary" type="submit">Filter</button>
    <a class="btn btn-ghost" href="{{ route('admin.users.index') }}">Reset</a>
  </form>

  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Lokasi</th>
            <th>Role</th>
            <th>Status Akun</th>
            <th>Daftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td style="color:var(--text2);font-size:12px">{{ $u->id_user }}</td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div style="width:34px;height:34px;border-radius:50%;background:var(--amber-bg);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--amber);flex-shrink:0">
                    {{ strtoupper(substr($u->nama,0,2)) }}
                  </div>
                  <span style="font-weight:600;font-size:14px">{{ $u->nama }}</span>
                </div>
              </td>
              <td style="font-size:13px;color:var(--text2)">{{ $u->email }}</td>
              <td style="font-size:13px">{{ $u->no_hp ?? '-' }}</td>
              <td style="font-size:13px;color:var(--text2)">{{ $u->lokasi ?? '-' }}</td>
              <td>
                @foreach($u->roles as $r)
                  <span class="badge {{ $r->nama_role==='admin'?'b-habis':($r->nama_role==='penjual'?'b-bekas':'b-diproses') }}" style="margin-right:2px">
                    {{ $r->nama_role }}
                  </span>
                @endforeach
              </td>
              <td>
                @php
                  $sc = match($u->status_akun) {
                    'aktif'    => 'b-selesai',
                    'menunggu' => 'b-menunggu',
                    default    => 'b-dibatalkan',
                  };
                @endphp
                <span class="badge {{ $sc }}">{{ $u->status_akun }}</span>
                @if($u->catatan_admin)
                  <p style="font-size:11px;color:var(--text2);margin-top:3px">{{ Str::limit($u->catatan_admin,40) }}</p>
                @endif
              </td>
              <td style="font-size:12px;color:var(--text2)">{{ $u->created_at->format('d M Y') }}</td>
              <td>
                @if(!$u->isAdmin())
                  <form method="POST" action="{{ route('admin.users.aksi', $u->id_user) }}" style="display:flex;flex-direction:column;gap:6px">
                    @csrf
                    @if($u->status_akun === 'aktif')
                      <input type="hidden" name="aksi" value="ditolak">
                      <button class="btn-sm" style="border-color:var(--red);color:var(--red)" type="submit"
                              onclick="return confirm('Nonaktifkan user ini?')">Nonaktifkan</button>
                    @else
                      <input type="hidden" name="aksi" value="aktif">
                      <button class="btn-sm" style="border-color:var(--green);color:var(--green)" type="submit">Aktifkan</button>
                    @endif
                  </form>
                @else
                  <span style="font-size:12px;color:var(--text2)">Admin</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center;padding:2rem;color:var(--text2)">Tidak ada pengguna ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div style="margin-top:1.5rem">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
