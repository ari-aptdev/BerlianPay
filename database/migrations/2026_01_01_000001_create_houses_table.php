<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('block', 10);
            $table->string('house_number', 20);
            $table->string('owner_name');
            $table->string('phone')->nullable();
            $table->string('nik', 16)->unique()->comment('Nomor Induk Kependudukan pemilik rumah');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['block', 'house_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
