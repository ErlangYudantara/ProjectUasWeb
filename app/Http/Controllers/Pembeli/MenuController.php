<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $q          = trim((string) $request->query('q', ''));
        $kategoriId = $request->integer('kategori') ?: null;

        $menus = Menu::with(['tenant', 'category'])
            ->whereHas('tenant', fn ($t) => $t->where('status', 'aktif'))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama_menu', 'like', "%{$q}%")
                      ->orWhere('deskripsi', 'like', "%{$q}%")
                      ->orWhereHas('tenant', fn ($t) => $t->where('nama_stan', 'like', "%{$q}%"));
                });
            })
            ->when($kategoriId, fn ($query) => $query->where('category_id', $kategoriId))
            ->orderByDesc('jumlah_ulasan')
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        $stanTunggal = null;
        if ($q !== '') {
            $cocok = Tenant::where('status', 'aktif')->where('nama_stan', 'like', "%{$q}%")->get();
            $stanTunggal = $cocok->count() === 1 ? $cocok->first() : null;
        }

        return view('menu.index', [
            'menus'       => $menus,
            'categories'  => Category::orderBy('id')->get(),
            'q'           => $q,
            'kategoriId'  => $kategoriId,
            'stanTunggal' => $stanTunggal,
        ]);
    }
}