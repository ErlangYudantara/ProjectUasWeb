<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguarded(function () {
            $tenant = Tenant::pluck('id', 'nama_stan');

            // [stan, kategori_id, nama, deskripsi, harga, label, tipe, rating, ulasan]
            // kategori: 1 Makanan Berat, 2 Soto & Kuah, 3 Minuman Segar, 4 Camilan & Snack
            $data = [
                ['Kedai Bu Sri', 1, 'Ayam Geprek Sambal Korek', 'Ayam krispi renyah diulek dengan cabai rawit segar pedas mantap + nasi hangat.', 16000, 'BEST SELLER', 'hijau', 4.9, 1200],
                ['Kedai Bu Sri', 1, 'Nasi Rames Campur Komplit', 'Nasi pulen dengan lauk orek tempeh manis, telur balado, serundeng daging, dan sambal.', 15000, 'PILIHAN HEMAT', 'emas', 4.8, 890],
                ['Kedai Bu Sri', 1, 'Ayam Bakar Madu UPN', 'Ayam bakar bumbu kecap madu khas Bu Sri dengan lalapan timun, kemangi, dan sambal terasi.', 17000, 'FAVORIT DOSEN', 'hijau', 4.9, 640],
                ['Kedai Bu Sri', 1, 'Ayam Geprek Keju Mozarella', 'Ayam krispi digeprek sambal bawang dilumuri lelehan keju mozarella gurih.', 20000, 'MENU SPESIAL', 'hijau', 4.9, 520],
                ['Kedai Bu Sri', 1, 'Paket Nasi Pecel Ayam Bu Sri', 'Nasi putih dengan sayuran rebus segar, siraman bumbu kacang medok khas Bu Sri, dan ayam goreng.', 18000, 'TRADISIONAL', 'hijau', 4.8, 410],
                ['Kedai Bu Sri', 1, 'Nasi Goreng Kampus Spesial', 'Nasi goreng bumbu rempah harum dengan suwiran ayam, bakso sapi, telur mata sapi, dan acar.', 15000, 'PILIHAN CEPAT', 'emas', 4.7, 380],
                ['Kedai Bu Sri', 4, 'Tahu Tempe Mendoan Crispy', 'Porsi lengkap tahu goreng renyah dan 3 lembar mendoan daun bawang dengan sambal kecap.', 8000, 'PELENGKAP', 'biru', 4.8, 300],
                ['Kedai Bu Sri', 3, 'Es Teh Manis Jumbo', 'Es teh manis segar ukuran jumbo, porsi kantin pas untuk pendamping makan ayam.', 4000, 'MINUMAN SEGAR', 'hijau', 4.9, 700],

                ['Dapur Nusantara', 2, 'Soto Ayam Lamongan Komplit', 'Kuah kuning gurih khas Lamongan bertabur koya renyah, suwiran ayam, soun, dan telur.', 18000, 'KUAH SEGAR', 'emas', 4.8, 750],
                ['Dapur Nusantara', 2, 'Nasi Rawon Daging Sapi', 'Kuah kluwek hitam rempah kaya rasa, potongan daging sapi empuk, tauge pendek, dan telur asin.', 22000, 'FAVORIT', 'biru', 4.9, 610],
                ['Dapur Nusantara', 2, 'Soto Betawi Susu', 'Kuah santan susu gurih dengan daging sapi empuk, disajikan dengan emping dan acar.', 20000, 'SPESIAL BETAWI', 'hijau', 4.7, 350],
                ['Dapur Nusantara', 1, 'Gado-gado Jakarta', 'Sayuran segar, lontong, tahu, tempe, dan telur dengan siraman bumbu kacang kental.', 14000, 'SEHAT', 'hijau', 4.6, 260],
                ['Dapur Nusantara', 3, 'Es Cendol Dawet', 'Cendol hijau kenyal, santan, dan gula merah cair dengan es serut segar.', 8000, 'SEGAR MANIS', 'emas', 4.7, 300],
                ['Dapur Nusantara', 4, 'Pisang Goreng Madu', 'Pisang kepok goreng renyah disiram madu dan taburan wijen.', 7000, 'CAMILAN', 'biru', 4.6, 220],

                ['Kopi & Minuman Veteran', 3, 'Es Jeruk Peras Murni Limo', 'Perasan jeruk peras asli segar dipadu gula tebu alami dengan es batu kristal higienis.', 7000, 'MINUMAN SEGAR', 'hijau', 4.9, 980],
                ['Kopi & Minuman Veteran', 3, 'Es Teh Manis Melati Jumbo', 'Seduhan teh melati wangi khas dengan gula tebu asli dalam ukuran jumbo.', 5000, 'PALING LARIS', 'biru', 4.9, 1500],
                ['Kopi & Minuman Veteran', 3, 'Jus Alpukat Kocok', 'Buah alpukat mentega asli dikocok dengan krimer manis dan saus cokelat.', 10000, 'SEGAR SEHAT', 'hijau', 4.9, 820],
                ['Kopi & Minuman Veteran', 3, 'Kopi Susu Gula Aren', 'Espresso, susu segar, dan gula aren asli dalam wadah tumbler ramah lingkungan.', 12000, 'BEST SELLER', 'hijau', 4.9, 1100],
                ['Kopi & Minuman Veteran', 3, 'Matcha Oat Latte', 'Matcha murni berpadu susu oat lembut, pilihan segar tanpa susu sapi.', 15000, 'MENU BARU', 'emas', 4.8, 430],
                ['Kopi & Minuman Veteran', 4, 'Roti Bakar Coklat Keju', 'Roti tebal dipanggang renyah dengan selai cokelat dan parutan keju.', 9000, 'PENDAMPING KOPI', 'biru', 4.7, 310],
            ];

            foreach ($data as [$stan, $kat, $nama, $desk, $harga, $label, $tipe, $rating, $ulasan]) {
                Menu::updateOrCreate(
                    ['tenant_id' => $tenant[$stan], 'nama_menu' => $nama],
                    [
                        'category_id'   => $kat,
                        'deskripsi'     => $desk,
                        'harga'         => $harga,
                        'stok'          => 50,
                        'label'         => $label,
                        'label_tipe'    => $tipe,
                        'rating'        => $rating,
                        'jumlah_ulasan' => $ulasan,
                        'status'        => 'tersedia',
                    ]
                );
            }
        });
    }
}