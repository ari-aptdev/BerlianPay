<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pakai raw SQL (bukan ->change()) biar gak perlu nambah dependency
        // doctrine/dbal yang berisiko kena masalah kayak riwayat project ini sebelumnya.
        DB::statement('ALTER TABLE houses MODIFY block VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE houses MODIFY block VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
