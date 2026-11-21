<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SellerReview;
use App\Models\User;
use App\Models\Role;

class SellerReviewSeeder extends Seeder
{
    public function run(): void
    {
        // ── Data pembeli fiktif untuk demo ──────────────────────────
        $demoBuyers = [
            ['nama' => 'Rizky Pratama',    'email' => 'rizky@demo.com',    'lokasi' => 'UGM Yogyakarta'],
            ['nama' => 'Siti Nurhaliza',   'email' => 'siti@demo.com',     'lokasi' => 'UNY Yogyakarta'],
            ['nama' => 'Budi Santoso',     'email' => 'budi@demo.com',     'lokasi' => 'UPN Yogyakarta'],
            ['nama' => 'Dewi Rahayu',      'email' => 'dewi@demo.com',     'lokasi' => 'UII Yogyakarta'],
            ['nama' => 'Fajar Nugroho',    'email' => 'fajar@demo.com',    'lokasi' => 'AMIKOM Yogyakarta'],
            ['nama' => 'Laila Safitri',    'email' => 'laila@demo.com',    'lokasi' => 'UAD Yogyakarta'],
            ['nama' => 'Hendra Kusuma',    'email' => 'hendra@demo.com',   'lokasi' => 'UMY Yogyakarta'],
        ];

        // ── Data review realistis ────────────────────────────────────
        $reviewData = [
            ['rating' => 5, 'comment' => 'Penjual sangat ramah dan responsif! Barang sesuai deskripsi, kondisi mulus. Transaksi COD lancar banget, recommended!'],
            ['rating' => 5, 'comment' => 'Mantap! Barang sampai tepat waktu, penjual amanah. Harga juga bisa nego dikit. Pasti balik lagi kalau ada yang butuh.'],
            ['rating' => 4, 'comment' => 'Oke banget, penjual fast respon. Sedikit terlambat pas COD tapi masih oke. Barang kondisi seperti yang dijanjikan.'],
            ['rating' => 4, 'comment' => 'Barang bagus, harga sepadan. Komunikasi lancar. Tempat COD strategis. Overall satisfied deh!'],
            ['rating' => 5, 'comment' => 'Transaksi paling nyaman yang pernah saya lakuin. Penjual jujur dan detail ngasih info produknya. 5 bintang!'],
            ['rating' => 3, 'comment' => 'Barang sesuai, tapi penjual agak susah dihubungi di awal. Setelah COD sih oke. Semoga next time lebih responsif.'],
            ['rating' => 5, 'comment' => 'Perfect! Penjual jelas dan terpercaya. Kondisi barang bahkan lebih bagus dari foto. Sangat direkomendasikan!'],
        ];

        // ── Pastikan role pembeli ada ────────────────────────────────
        $rolePembeli = Role::where('nama_role', 'pembeli')->first();
        if (!$rolePembeli) {
            $this->command->warn('⚠️  Role pembeli tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // ── Ambil semua penjual aktif ────────────────────────────────
        $sellers = User::whereHas('roles', function ($q) {
            $q->where('nama_role', 'penjual');
        })->where('status_akun', 'aktif')->get();

        if ($sellers->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada penjual aktif. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // ── Buat / ambil pembeli fiktif ──────────────────────────────
        $buyers = collect();
        foreach ($demoBuyers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nama'        => $data['nama'],
                    'password'    => Hash::make('password'),
                    'no_hp'       => '08' . rand(100000000, 999999999),
                    'lokasi'      => $data['lokasi'],
                    'status_akun' => 'aktif',
                    'created_at'  => now(),
                ]
            );
            // Attach role pembeli jika belum punya
            if (!$user->roles()->where('nama_role', 'pembeli')->exists()) {
                $user->roles()->attach($rolePembeli->id_role);
            }
            $buyers->push($user);
        }

        // ── Seed review: setiap pembeli → setiap penjual ─────────────
        $inserted = 0;
        foreach ($sellers as $seller) {
            foreach ($buyers as $index => $buyer) {
                if ($buyer->id_user === $seller->id_user) continue;

                $review = $reviewData[$index % count($reviewData)];

                SellerReview::updateOrCreate(
                    [
                        'reviewer_id' => $buyer->id_user,
                        'seller_id'   => $seller->id_user,
                    ],
                    [
                        'rating'     => $review['rating'],
                        'comment'    => $review['comment'],
                        'created_at' => now()->subDays(rand(1, 90)),
                        'updated_at' => now()->subDays(rand(0, 5)),
                    ]
                );
                $inserted++;
            }
        }

        $this->command->info("✅ SellerReviewSeeder: {$inserted} review berhasil diseed untuk {$sellers->count()} penjual.");
    }
}
