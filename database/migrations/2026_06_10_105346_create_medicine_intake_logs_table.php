<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_intake_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->dateTime('scheduled_end_at');
            $table->string('status', 20);
            $table->dateTime('taken_at')->nullable();
            $table->dateTime('missed_at')->nullable();
            $table->timestamps();

            $table->unique(['medicine_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_intake_logs');
    }
};
