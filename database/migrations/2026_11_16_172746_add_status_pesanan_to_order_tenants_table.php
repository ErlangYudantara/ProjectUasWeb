<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_tenants', function (Blueprint $table) {
            $table->enum('status_pesanan', [
                'menunggu_pembayaran',
                'menunggu_konfirmasi',
                'sedang_disiapkan',
                'siap_diambil',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran')->after('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_tenants', function (Blueprint $table) {
            $table->dropColumn('status_pesanan');
        });
    }
};
