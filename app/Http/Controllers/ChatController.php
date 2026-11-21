<?php
namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller {
    public function index($id) {
        $user      = Auth::user();
        $transaksi = Transaksi::with(['chat.pengirim','pembeli','penjual'])
            ->where('id_transaksi',$id)
            ->where(fn($q) => $q->where('id_pembeli',$user->id_user)->orWhere('id_penjual',$user->id_user))
            ->firstOrFail();
        Chat::where('id_transaksi',$id)->where('id_pengirim','!=',$user->id_user)->update(['dibaca'=>1]);
        return view('transaksi.chat', compact('transaksi','user'));
    }
    public function store(Request $request, $id) {
        $user      = Auth::user();
        $transaksi = Transaksi::where('id_transaksi',$id)
            ->where(fn($q) => $q->where('id_pembeli',$user->id_user)->orWhere('id_penjual',$user->id_user))
            ->firstOrFail();
        $request->validate(['pesan'=>'required|string|max:1000']);
        Chat::create(['id_transaksi'=>$id,'id_pengirim'=>$user->id_user,'pesan'=>$request->pesan,'created_at'=>now()]);
        return redirect()->route('chat.index',$id);
    }
}
