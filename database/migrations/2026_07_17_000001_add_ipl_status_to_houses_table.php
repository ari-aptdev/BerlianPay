<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->enum('ipl_status', ['rukem', 'non_rukem'])->default('non_rukem')->after('nik');
            $table->timestamp('rukem_joined_at')->nullable()->after('ipl_status');
        });
    }

    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn(['ipl_status', 'rukem_joined_at']);
        });
    }
};
