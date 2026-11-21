@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto py-20">

    <form
        action="/produk/store"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-10 rounded-2xl shadow"
    >

        @csrf

        <h1 class="text-3xl font-bold mb-8">
            Upload Produk
        </h1>

        <input
            type="text"
            name="nama_produk"
            placeholder="Nama Produk"
            class="w-full border p-3 rounded-xl mb-4"
        >

        <input
            type="number"
            name="harga"
            placeholder="Harga"
            class="w-full border p-3 rounded-xl mb-4"
        >

        <textarea
            name="deskripsi"
            placeholder="Deskripsi"
            class="w-full border p-3 rounded-xl mb-4"
        ></textarea>

        <input
            type="file"
            name="foto"
            class="w-full border p-3 rounded-xl mb-4"
        >

        <button class="bg-orange-500 text-white px-6 py-3 rounded-xl">
            Upload
        </button>

    </form>

</div>

@endsection