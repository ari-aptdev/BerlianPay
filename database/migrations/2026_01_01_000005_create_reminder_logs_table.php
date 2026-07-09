<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['email', 'whatsapp']);
            $table->enum('reminder_type', ['h_minus_3', 'h_day', 'overdue_followup']);
            $table->date('sent_date')->comment('Tanggal pengiriman, dipakai untuk mencegah kirim dobel di hari yang sama');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            // Mencegah reminder dobel di hari, tipe, channel yang sama untuk rumah yang sama
            $table->unique(['house_id', 'channel', 'reminder_type', 'sent_date'], 'reminder_unique_per_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
