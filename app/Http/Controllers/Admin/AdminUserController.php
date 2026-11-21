<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->orderByDesc('id_user');

        if ($request->cari) {
            $q = $request->cari;
            $query->where(fn($qb) => $qb->where('nama','like',"%$q%")->orWhere('email','like',"%$q%"));
        }
        if ($request->role) {
            $query->whereHas('roles', fn($r) => $r->where('nama_role', $request->role));
        }
        if ($request->status) $query->where('status_akun', $request->status);

        $users = $query->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function aksi(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if (in_array($request->aksi, ['aktif','ditolak','menunggu'])) {
            $user->update(['status_akun' => $request->aksi, 'catatan_admin' => $request->catatan_admin]);
        }
        return back()->with('success', '✅ Status user diperbarui!');
    }
}
