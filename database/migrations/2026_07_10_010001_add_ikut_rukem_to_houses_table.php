<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->boolean('ikut_rukem')->default(false)->after('id');
        });
    }

    public function down()
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn('ikut_rukem');
        });
    }
};
