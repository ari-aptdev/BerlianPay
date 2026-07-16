<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', ['monthly', 'rukem_registration'])->default('monthly')->after('house_id');
            $table->enum('ipl_status', ['rukem', 'non_rukem'])->nullable()->after('amount')
                ->comment('Snapshot status rumah saat tagihan dibuat, biar histori gak berubah walau status berubah nanti');
            $table->json('breakdown')->nullable()->after('ipl_status')
                ->comment('Snapshot rincian komponen iuran saat tagihan dibuat, biar histori gak berubah walau tarif diedit admin nanti');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'ipl_status', 'breakdown']);
        });
    }
};
