<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\ProdukFoto;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin   = Role::create(['nama_role' => 'admin']);
        $penjual = Role::create(['nama_role' => 'penjual']);
        $pembeli = Role::create(['nama_role' => 'pembeli']);

        // Kategori
        $kategori = [
            'Buku & Modul', 'Elektronik', 'Alat Tulis & Stationery',
            'Fashion & Pakaian', 'Perabot Kos', 'Olahraga',
            'Kendaraan & Aksesori', 'Makanan & Minuman', 'Jasa & Lainnya', 'hewan',
        ];
        foreach ($kategori as $k) {
            Kategori::create(['nama_kategori' => $k]);
        }

        // Admin user
        $adminUser = User::create([
            'nama'        => 'Admin Preloved',
            'email'       => 'admin@preloved.mhs',
            'password'    => Hash::make('admin123'),
            'no_hp'       => '081234567890',
            'lokasi'      => 'Kampus Pusat',
            'status_akun' => 'aktif',
            'created_at'  => now(),
        ]);
        $adminUser->roles()->attach($admin->id_role);

        // Demo pembeli
        $pembeliUser = User::create([
            'nama'        => 'Ahmad Ghilman',
            'email'       => 'pembeli@demo.com',
            'password'    => Hash::make('password'),
            'no_hp'       => '081393978630',
            'lokasi'      => 'UPN Yogyakarta',
            'status_akun' => 'aktif',
            'created_at'  => now(),
        ]);
        $pembeliUser->roles()->attach($pembeli->id_role);

        // Demo penjual
        $penjualUser = User::create([
            'nama'        => 'Toko Preloved Jaya',
            'email'       => 'penjual@demo.com',
            'password'    => Hash::make('password'),
            'no_hp'       => '089876543210',
            'lokasi'      => 'UPN Yogyakarta',
            'foto_ktm'    => null,
            'status_akun' => 'aktif',
            'created_at'  => now(),
        ]);
        $penjualUser->roles()->attach($penjual->id_role);

        // Contoh Produk
        $produkData = [
            [
                'nama' => 'Buku Algoritma & Struktur Data',
                'kategori' => 'Buku & Modul',
                'harga' => 75000,
                'deskripsi' => 'Buku bekas tapi masih bagus, semua halaman lengkap, ada catatan tapi tidak banyak.'
            ],
            [
                'nama' => 'Headphone Wireless Bluetooth',
                'kategori' => 'Elektronik',
                'harga' => 150000,
                'deskripsi' => 'Headphone bluetooth bekas, masih sangat bagus, baterai awet, semua aksesoris lengkap.'
            ],
            [
                'nama' => 'Alat Tulis Lengkap Satu Set',
                'kategori' => 'Alat Tulis & Stationery',
                'harga' => 50000,
                'deskripsi' => 'Paket alat tulis termasuk bolpoin, pensil, penggaris, penghapus, dan kotak pensil.'
            ],
            [
                'nama' => 'Kaos Distro Pria Warna Hitam',
                'kategori' => 'Fashion & Pakaian',
                'harga' => 45000,
                'deskripsi' => 'Kaos bekas, kondisi masih bagus, ukuran L, bahan katun 100% tebal dan nyaman.'
            ],
            [
                'nama' => 'Lemari Kabinet Kecil untuk Kos',
                'kategori' => 'Perabot Kos',
                'harga' => 250000,
                'deskripsi' => 'Lemari plastik bekas, tinggi sekitar 1 meter, cocok untuk menyimpan barang kos.'
            ],
            [
                'nama' => 'Raket Badminton Victor',
                'kategori' => 'Olahraga',
                'harga' => 180000,
                'deskripsi' => 'Raket badminton bekas, masih terawat, cocok untuk pemain kasual hingga menengah.'
            ],
            [
                'nama' => 'Helm Sepeda Motor Full Face',
                'kategori' => 'Kendaraan & Aksesori',
                'harga' => 220000,
                'deskripsi' => 'Helm bekas, masih bagus, sudah standar SNI, visor masih jernih, grafis masih keren.'
            ],
            [
                'nama' => 'Tumbler Stainless Steel 500ml',
                'kategori' => 'Makanan & Minuman',
                'harga' => 65000,
                'deskripsi' => 'Tumbler stainless steel, tahan panas dan dingin, desain minimalis, sangat fungsional.'
            ],
        ];

        foreach ($produkData as $data) {
            $kategoriId = Kategori::where('nama_kategori', $data['kategori'])->first()?->id_kategori;
            
            if ($kategoriId) {
                $produk = Produk::create([
                    'id_user' => $penjualUser->id_user,
                    'id_kategori' => $kategoriId,
                    'nama_produk' => $data['nama'],
                    'deskripsi' => $data['deskripsi'],
                    'harga' => $data['harga'],
                    'kondisi' => 'Bekas',
                    'status' => 'tersedia',
                    'created_at' => now(),
                ]);

                // Tambah foto produk (dummy image placeholders)
                ProdukFoto::create([
                    'id_produk' => $produk->id_produk,
                    'foto' => 'produk_' . uniqid() . '.jpg',
                ]);
            }
        }

        // Seed seller reviews
        $this->call(SellerReviewSeeder::class);
    }
}
