<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipl_rates', function (Blueprint $table) {
            $table->id();
            $table->string('house_type', 50);
            $table->unsignedBigInteger('nominal');
            $table->date('effective_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipl_rates');
    }
};
