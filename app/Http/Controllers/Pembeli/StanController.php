<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class StanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $tenants = Tenant::where('status', 'aktif')
            ->withCount('menus')
            ->withAvg('menus', 'rating')
            ->when($q !== '', fn ($query) => $query->where('nama_stan', 'like', '%' . addcslashes($q, '%_\\') . '%'))
            ->orderBy('id')
            ->get();

        return view('stan.index', compact('tenants', 'q'));
    }
}