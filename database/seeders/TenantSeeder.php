<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguarded(function () {
            $stan = [
                [
                    'email' => 'bu.sri@kantin.test', 'nama' => 'Bu Sri',
                    'nama_stan' => 'Kedai Bu Sri',
                    'deskripsi' => 'Ayam Geprek sambal bawang pedas nikmat, nasi hemat mahasiswa, dan aneka lauk rumahan bergizi.',
                    'buka' => '07:30', 'tutup' => '16:30', 'foto' => 'images/stan-1.jpg',
                ],
                [
                    'email' => 'nusantara@kantin.test', 'nama' => 'Pak Joko',
                    'nama_stan' => 'Dapur Nusantara',
                    'deskripsi' => 'Soto ayam kaldu rempah kuning, soto Betawi gurih, serta aneka masakan tradisional nusantara.',
                    'buka' => '08:00', 'tutup' => '17:00', 'foto' => 'images/stan-2.jpg',
                ],
                [
                    'email' => 'veteran@kantin.test', 'nama' => 'Mbak Rani',
                    'nama_stan' => 'Kopi & Minuman Veteran',
                    'deskripsi' => 'Kopi susu gula aren khas kampus, matcha oat, teh herbal segar, dan jus buah asli penyemangat kuliah.',
                    'buka' => '07:30', 'tutup' => '18:00', 'foto' => 'images/stan-3.jpg',
                ],
            ];

            foreach ($stan as $s) {
                // Login penjual: email di atas, password "password123"
                $user = User::updateOrCreate(
                    ['email' => $s['email']],
                    ['nama' => $s['nama'], 'password' => Hash::make('password123'), 'role' => 'penjual']
                );

                Tenant::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama_stan' => $s['nama_stan'],
                        'deskripsi' => $s['deskripsi'],
                        'lokasi'    => 'Gedung Dr. Soetomo Lt. 1',
                        'foto'      => $s['foto'],
                        'jam_buka'  => $s['buka'],
                        'jam_tutup' => $s['tutup'],
                        'status'    => 'aktif',
                    ]
                );
            }
        });
    }
}