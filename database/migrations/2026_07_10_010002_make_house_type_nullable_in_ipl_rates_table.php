<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ipl_rates', function (Blueprint $table) {
            $table->string('house_type')->default('')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('ipl_rates', function (Blueprint $table) {
            $table->string('house_type')->nullable(false)->change();
        });
    }
};
