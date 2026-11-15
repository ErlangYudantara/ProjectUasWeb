<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('keranjang.index');
    }

    // Data terbaru menu untuk isi keranjang (harga & stok selalu dari database)
    public function detail(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(50)
            ->values();

        $menus = Menu::with('tenant')
            ->whereIn('id', $ids)
            ->get()
            ->map(fn ($m) => [
                'id'        => $m->id,
                'nama'      => $m->nama_menu,
                'deskripsi' => $m->deskripsi,
                'harga'     => (int) $m->harga,
                'foto'      => $m->foto_url,
                'stanId'    => $m->tenant_id,
                'stan'      => optional($m->tenant)->nama_stan,
                'stok'      => (int) $m->stok,
                'tersedia'  => $m->status === 'tersedia'
                               && $m->stok > 0
                               && optional($m->tenant)->status === 'aktif',
            ]);

        return response()->json($menus->keyBy('id'));
    }
}