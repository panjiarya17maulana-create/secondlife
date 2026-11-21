<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class AdminVerifikasiController extends Controller {
    public function index() {
        $penjualMenunggu = User::with('roles')
            ->where('status_akun','menunggu')
            ->whereHas('roles', fn($q) => $q->where('nama_role','penjual'))
            ->paginate(20);
        return view('admin.verifikasi', compact('penjualMenunggu'));
    }
    public function proses(Request $request, $id) {
        $user = User::findOrFail($id);
        $request->validate(['status'=>'required|in:aktif,ditolak']);

        if ($request->status === 'ditolak') {
            // Kembalikan status ke 'aktif' agar user masih bisa login sebagai pembeli
            $user->update([
                'status_akun'   => 'aktif',
                'catatan_admin' => $request->catatan_admin,
            ]);
            // Hapus role penjual agar user kembali menjadi pembeli
            $rolePenjual = \App\Models\Role::where('nama_role', 'penjual')->first();
            if ($rolePenjual) {
                $user->roles()->detach($rolePenjual->id_role);
            }
            $msg = '❌ Penjual ditolak. Akun dikembalikan sebagai pembeli.';
        } else {
            $user->update([
                'status_akun'   => 'aktif',
                'catatan_admin' => $request->catatan_admin,
            ]);
            $msg = '✅ Penjual diverifikasi!';
        }

        return back()->with('success', $msg);
    }
}
