<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Order;

class StatusPesananController extends Controller
{
    public function show($id)
    {
        $order = Order::with([
            'user',
            'details.menu.tenant',
            'tenants',
            'orderTenants',
            'payment',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        return view('pembeli.status-pesanan.status', compact('order'));
    }
}
