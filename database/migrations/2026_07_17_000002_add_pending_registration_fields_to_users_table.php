<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Data yang diisi warga pas daftar mandiri, TAPI belum masuk ke tabel
            // houses sampai admin approve akunnya (lihat poin 5: patokan data
            // warga & rumah adalah akun warga yang sudah di-approve).
            $table->string('pending_block')->nullable()->after('nik');
            $table->string('pending_house_number')->nullable()->after('pending_block');
            $table->boolean('pending_wants_rukem')->default(false)->after('pending_house_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pending_block', 'pending_house_number', 'pending_wants_rukem']);
        });
    }
};
