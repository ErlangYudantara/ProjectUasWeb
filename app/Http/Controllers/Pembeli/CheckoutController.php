<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderTenant;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Memproses checkout seluruh keranjang
     * menjadi satu transaksi dan satu pembayaran.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id' => ['required', 'integer', 'distinct'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.catatan' => ['nullable', 'string', 'max:100'],
            'opsi_penyajian' => ['required', 'in:makan,bungkus'],
        ]);

        $order = DB::transaction(function () use ($validated, $request) {

            $menuIds = collect($validated['items'])
                ->pluck('id')
                ->sort()
                ->values();

            $menus = Menu::with('tenant')
                ->whereIn('id', $menuIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($menus->count() !== $menuIds->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Ada menu yang sudah tidak tersedia.',
                ]);
            }

            $groups = [];
            $subtotal = 0;
            $totalQty = 0;

            foreach ($validated['items'] as $item) {
                $menu = $menus->get($item['id']);
                $qty = (int) $item['qty'];

                if (
                    ! $menu->tenant ||
                    $menu->tenant->status !== 'aktif' ||
                    $menu->status !== 'tersedia' ||
                    $menu->stok < $qty
                ) {
                    throw ValidationException::withMessages([
                        'items' => "Menu {$menu->nama_menu} sedang habis atau tidak tersedia.",
                    ]);
                }

                $tenantId = $menu->tenant_id;
                $harga = (float) $menu->harga;
                $itemSubtotal = $harga * $qty;

                if (!isset($groups[$tenantId])) {
                    $groups[$tenantId] = [];
                }

                $groups[$tenantId][] = [
                    'menu' => $menu,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $itemSubtotal,
                    'catatan' => $item['catatan'] ?? null,
                ];

                $subtotal += $itemSubtotal;
                $totalQty += $qty;
            }

            // Diskon Wadah Hijau Rp500 per item.
            $diskon = min($totalQty * 500, $subtotal);

            // Biaya bungkus sementara Rp0.
            $biayaBungkus = 0;

            $totalBayar = $subtotal - $diskon + $biayaBungkus;

            if ($totalBayar <= 0) {
                throw ValidationException::withMessages([
                    'items' => 'Total pembayaran tidak valid.',
                ]);
            }

            // Buat nomor transaksi unik.
            do {
                $nomorPesanan = 'KD'
                    . now()->format('ymdHis')
                    . Str::upper(Str::random(4));
            } while (
                Order::where('nomor_pesanan', $nomorPesanan)->exists()
            );

            // Satu order utama untuk seluruh stan.
            $order = Order::create([
                'user_id' => $request->user()->id,
                'tenant_id' => null,
                'nomor_pesanan' => $nomorPesanan,
                'tanggal_pesanan' => now(),
                'total_harga' => $totalBayar,
                'opsi_penyajian' => $validated['opsi_penyajian'],
                'status_pesanan' => 'menunggu_pembayaran',
            ]);

            // Hubungkan seluruh stan ke satu order.
            foreach ($groups as $tenantId => $details) {
                OrderTenant::create([
                    'order_id' => $order->id,
                    'tenant_id' => $tenantId,
                    'status_pesanan' => 'menunggu_pembayaran',
                ]);

                foreach ($details as $detail) {
                    $order->details()->create([
                        'menu_id' => $detail['menu']->id,
                        'jumlah' => $detail['qty'],
                        'harga' => $detail['harga'],
                        'subtotal' => $detail['subtotal'],
                        'catatan' => $detail['catatan'],
                    ]);

                    $detail['menu']->decrement('stok', $detail['qty']);
                }
            }

            // Satu pembayaran untuk seluruh transaksi.
            Payment::create([
                'order_id' => $order->id,
                'metode_pembayaran' => 'qris',
                'jumlah_bayar' => $totalBayar,
                'status_pembayaran' => 'pending',
            ]);

            // Catat status awal transaksi.
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'menunggu_pembayaran',
                'updated_by' => $request->user()->id,
                'updated_at' => now(),
            ]);

            return $order;
        });

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'order_id' => $order->id,
            'redirect' => route('pembayaran.show', $order->id),
        ]);
    }

    /**
     * Menampilkan halaman pembayaran QRIS.
     */
    public function show($id)
    {
        $order = Order::with([
            'details.menu.tenant',
            'tenants',
            'payment',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('pembeli.pembayaran.qris', compact('order'));
    }

    /**
     * Simulasi konfirmasi pembayaran.
     * Bukan verifikasi pembayaran dari bank/payment gateway.
     */
    public function simulatePayment($id)
    {
        $order = Order::with('payment')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if (!$order->payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Jika pembayaran sudah berhasil, langsung ke status pesanan.
        if ($order->payment->status_pembayaran === 'berhasil') {
            return redirect()
                ->route('status-pesanan.show', $order->id)
                ->with('success', 'Pembayaran sudah berhasil.');
        }

        if ($order->payment->status_pembayaran !== 'pending') {
            return back()->with('error', 'Pembayaran tidak dapat diproses.');
        }

        DB::transaction(function () use ($order) {

            // Ubah status pembayaran.
            $order->payment()->update([
                'status_pembayaran' => 'berhasil',
                'tanggal_bayar' => now(),
            ]);

            // Ubah status pesanan utama.
            $order->update([
                'status_pesanan' => 'menunggu_konfirmasi',
            ]);

            // Ubah status seluruh stan.
            OrderTenant::where('order_id', $order->id)
                ->update([
                    'status_pesanan' => 'menunggu_konfirmasi',
                ]);

            // Catat riwayat perubahan status.
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'menunggu_konfirmasi',
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);
        });

        // Arahkan pembeli ke halaman Status Pesanan.
        return redirect()
            ->route('status-pesanan.show', $order->id)
            ->with(
                'success',
                'Pembayaran berhasil. Pesananmu menunggu konfirmasi stan.'
            );
    }
}
