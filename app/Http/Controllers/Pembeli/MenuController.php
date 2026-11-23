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
        $stanId = $request->integer('stan') ?: null;
        if (! $stanId) {
            return redirect()->route('stan.index');
        }

        $stan = Tenant::where('status', 'aktif')->findOrFail($stanId);

        $q          = trim((string) $request->query('q', ''));
        $kategoriId = $request->integer('kategori') ?: null;
        $like       = '%' . addcslashes($q, '%_\\') . '%';

        $menus = Menu::with(['tenant', 'category'])
            ->where('tenant_id', $stan->id)
            ->when($q !== '', function ($query) use ($like) {
                $query->where(function ($w) use ($like) {
                    $w->where('nama_menu', 'like', $like)
                      ->orWhere('deskripsi', 'like', $like);
                });
            })
            ->when($kategoriId, fn ($query) => $query->where('category_id', $kategoriId))
            ->orderByDesc('jumlah_ulasan')
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return view('menu.index', [
            'menus'       => $menus,
            'categories'  => Category::orderBy('id')->get(),
            'q'           => $q,
            'kategoriId'  => $kategoriId,
            'stanId'      => $stan->id,
            'stanTunggal' => $stan,
        ]);
    }
}