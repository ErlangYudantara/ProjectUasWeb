<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Model::unguarded(function () {
            $data = [
                1 => 'Makanan Berat',
                2 => 'Soto & Kuah',
                3 => 'Minuman Segar',
                4 => 'Camilan & Snack',
            ];

            foreach ($data as $id => $nama) {
                Category::updateOrCreate(['id' => $id], ['nama_kategori' => $nama]);
            }
        });
    }
}