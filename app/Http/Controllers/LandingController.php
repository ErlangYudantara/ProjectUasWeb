<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        // Data sementara (statis). Nanti ganti dengan Tenant::with('menus')->get()
        $tenants = [
            [
                'no'       => '01',
                'nama'     => 'Kedai Bu Sri',
                'deskripsi'=> 'Ayam Geprek sambal bawang pedas nikmat, nasi hemat mahasiswa, dan aneka lauk rumahan bergizi.',
                'jam'      => '07.30 - 16.30 WIB',
                'harga'    => 12000,
                'rating'   => '4.8',
                'tag'      => 'Pondok Labu',
                'tag_tipe' => 'hijau',
                'foto'     => 'images/stan-1.jpg',
            ],
            [
                'no'       => '02',
                'nama'     => 'Dapur Nusantara',
                'deskripsi'=> 'Soto ayam kaldu rempah kuning, soto Betawi gurih, serta aneka masakan tradisional nusantara.',
                'jam'      => '08.00 - 17.00 WIB',
                'harga'    => 15000,
                'rating'   => '4.9',
                'tag'      => 'Pondok Labu',
                'tag_tipe' => 'hijau',
                'foto'     => 'images/stan-2.jpg',
            ],
            [
                'no'       => '03',
                'nama'     => 'Kopi & Minuman Veteran',
                'deskripsi'=> 'Kopi susu gula aren khas kampus, matcha oat, teh herbal segar, dan jus buah asli penyemangat kuliah.',
                'jam'      => '07.30 - 18.00 WIB',
                'harga'    => 8000,
                'rating'   => '4.9',
                'tag'      => 'Diskon Tumbler',
                'tag_tipe' => 'emas',
                'foto'     => 'images/stan-3.jpg',
            ],
        ];

        return view('landing.index', compact('tenants'));
    }
}
