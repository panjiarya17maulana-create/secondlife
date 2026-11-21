<?php
namespace App\Http\Controllers;
use App\Models\RequestKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestKategoriController extends Controller {
    public function index() {
        $list = RequestKategori::with('user','kategori')->where('id_user',Auth::id())->orderByDesc('created_at')->get();
        return view('request_kategori', compact('list'));
    }
    public function store(Request $request) {
        $request->validate(['nama_usulan'=>'required|string|max:100','alasan'=>'nullable|string']);
        RequestKategori::create(['id_user'=>Auth::id(),'nama_usulan'=>$request->nama_usulan,'alasan'=>$request->alasan,'status'=>'menunggu','created_at'=>now()]);
        return back()->with('success','✅ Request kategori terkirim!');
    }
}
