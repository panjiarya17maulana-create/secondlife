<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\JadwalCod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    public function checkout($id)
    {
        $user   = Auth::user();
        $produk = Produk::with(['kategori', 'penjual', 'foto'])
            ->where('id_produk', $id)
            ->where('status', 'tersedia')
            ->firstOrFail();

        if ($produk->penjual->id_user == $user->id_user) {
            return redirect()->route('produk.detail', $id)
                ->with('error', 'Tidak bisa membeli produk sendiri.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak bisa melakukan transaksi.');
        }

        $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        return view('transaksi.checkout', compact('produk', 'hariList'));
    }

    public function prosesCheckout(Request $request, $id)
    {
        $user   = Auth::user();
        $produk = Produk::where('id_produk', $id)->where('status', 'tersedia')->firstOrFail();

        $request->validate([
            'tanggal'    => 'required|date|after_or_equal:today',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'waktu'      => 'required',
            'lokasi_cod' => 'required|string|max:255',
            'catatan'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $user, $produk, &$trxId) {
            $trx = Transaksi::create([
                'id_pembeli'  => $user->id_user,
                'id_penjual'  => $produk->id_user,
                'total_harga' => $produk->harga,
                'status'      => 'menunggu_konfirmasi',
                'created_at'  => now(),
            ]);

            DetailTransaksi::create([
                'id_transaksi' => $trx->id_transaksi,
                'id_produk'    => $produk->id_produk,
                'qty'          => 1,
                'harga_satuan' => $produk->harga,
            ]);

            JadwalCod::create([
                'id_transaksi' => $trx->id_transaksi,
                'tanggal'      => $request->tanggal,
                'hari'         => $request->hari,
                'waktu'        => $request->waktu,
                'lokasi_cod'   => $request->lokasi_cod,
                'catatan'      => $request->catatan,
                'status_penjual'=> 'menunggu',
                'created_at'   => now(),
            ]);

            $produk->update(['status' => 'habis']);
            $trxId = $trx->id_transaksi;
        });

        return redirect()->route('checkout.sukses', $trxId);
    }

    public function sukses($id)
    {
        $transaksi = Transaksi::with(['detail.produk.foto', 'jadwalCod', 'penjual'])
            ->where('id_transaksi', $id)
            ->where('id_pembeli', Auth::id())
            ->firstOrFail();

        return view('transaksi.sukses', compact('transaksi'));
    }

    public function index(Request $request)
    {
        $user   = Auth::user();
        $filter = in_array($request->status, ['menunggu_konfirmasi','dikonfirmasi','diproses','selesai','dibatalkan']) ? $request->status : '';

        // Transaksi sebagai pembeli
        $queryPembeli = Transaksi::with(['detail.produk.foto', 'penjual'])
            ->where('id_pembeli', $user->id_user)
            ->orderByDesc('created_at');
        if ($filter) $queryPembeli->where('status', $filter);
        $transaksiList = $queryPembeli->get();

        // Transaksi sebagai penjual
        $queryPenjual = Transaksi::with(['detail.produk.foto', 'pembeli'])
            ->where('id_penjual', $user->id_user)
            ->orderByDesc('created_at');
        if ($filter) $queryPenjual->where('status', $filter);
        $transaksiPenjual = $queryPenjual->get();

        return view('transaksi.index', compact('transaksiList', 'transaksiPenjual', 'filter'));
    }

    public function detail($id)
    {
        $user      = Auth::user();
        $transaksi = Transaksi::with(['detail.produk.foto', 'penjual', 'pembeli', 'jadwalCod', 'chat.pengirim'])
            ->where('id_transaksi', $id)
            ->where(function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->orWhere('id_penjual', $user->id_user);
            })
            ->firstOrFail();

        return view('transaksi.detail', compact('transaksi', 'user'));
    }

    public function aksi(Request $request, $id)
    {
        $user      = Auth::user();
        $transaksi = Transaksi::where('id_transaksi', $id)
            ->where(function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->orWhere('id_penjual', $user->id_user);
            })
            ->firstOrFail();

        $aksi = $request->action;

        if (!in_array($aksi, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        // ── Fitur #4: Wajib upload foto bukti COD sebelum bisa selesai ──
        if ($aksi === 'selesai') {
            if ($transaksi->status !== 'dikonfirmasi') {
                return back()->with('error', 'Transaksi harus berstatus dikonfirmasi sebelum diselesaikan.');
            }

            $request->validate([
                'foto_bukti' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            ], [
                'foto_bukti.required' => '📸 Foto bukti COD wajib diunggah sebelum menyelesaikan transaksi.',
                'foto_bukti.image'    => 'File harus berupa gambar.',
                'foto_bukti.max'      => 'Ukuran foto maksimal 4 MB.',
            ]);

            $file  = $request->file('foto_bukti');
            $nama  = 'bukti_' . $id . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('bukti_cod', $nama, 'public');

            // Hapus foto lama jika ada
            if ($transaksi->foto_bukti) {
                Storage::disk('public')->delete('bukti_cod/' . $transaksi->foto_bukti);
            }

            $transaksi->update(['status' => 'selesai', 'foto_bukti' => $nama]);
            return redirect()->route('transaksi.index')->with('success', '✅ Transaksi diselesaikan! Foto bukti tersimpan.');
        }

        // Aksi dibatalkan
        $transaksi->update(['status' => 'dibatalkan']);
        foreach ($transaksi->detail as $dt) {
            Produk::where('id_produk', $dt->id_produk)
                ->where('status', 'habis')
                ->update(['status' => 'tersedia']);
        }
        return redirect()->route('transaksi.index')->with('error', 'Transaksi dibatalkan.');
    }

    // ── Fitur #2: Edit/Reschedule Jadwal COD (UPDATE, tidak hapus transaksi) ──
    public function editJadwalCod(Request $request, $id)
    {
        $user      = Auth::user();
        $transaksi = Transaksi::where('id_transaksi', $id)
            ->where(function ($q) use ($user) {
                $q->where('id_pembeli', $user->id_user)
                  ->orWhere('id_penjual', $user->id_user);
            })
            ->whereIn('status', ['dikonfirmasi', 'menunggu_konfirmasi'])
            ->firstOrFail();

        $request->validate([
            'tanggal'    => 'required|date|after_or_equal:today',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'waktu'      => 'required',
            'lokasi_cod' => 'required|string|max:255',
            'catatan'    => 'nullable|string',
        ]);

        // UPDATE baris jadwal yang ada — tanpa DELETE, riwayat transaksi aman
        $jadwal = $transaksi->jadwalCod;
        if (!$jadwal) {
            return back()->with('error', 'Jadwal COD tidak ditemukan.');
        }

        $jadwal->update([
            'tanggal'    => $request->tanggal,
            'hari'       => $request->hari,
            'waktu'      => $request->waktu,
            'lokasi_cod' => $request->lokasi_cod,
            'catatan'    => $request->catatan,
        ]);

        return redirect()->route('transaksi.detail', $id)
            ->with('success', '📅 Jadwal COD berhasil diperbarui!');
    }
}
