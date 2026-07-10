<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ipl_rates', function (Blueprint $table) {
            $table->string('category')->nullable()->after('id'); // 'rukem' / 'non_rukem'
            $table->unsignedInteger('kas')->default(0)->after('category');
            $table->unsignedInteger('sampah')->default(0)->after('kas');
            $table->unsignedInteger('kebersihan')->default(0)->after('sampah');
            $table->unsignedInteger('rukem')->default(0)->after('kebersihan');
            $table->unsignedInteger('total')->default(0)->after('rukem');
        });
    }

    public function down()
    {
        Schema::table('ipl_rates', function (Blueprint $table) {
            $table->dropColumn(['category', 'kas', 'sampah', 'kebersihan', 'rukem', 'total']);
        });
    }
};
