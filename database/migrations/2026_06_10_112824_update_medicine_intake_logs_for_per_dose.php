<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->dropUnique(['medicine_id', 'scheduled_date']);
            $table->dropColumn(['scheduled_date', 'scheduled_end_at']);
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->after('medicine_id');
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->foreign('medicine_id')->references('id')->on('medicines')->cascadeOnDelete();
            $table->unique(['medicine_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->dropUnique(['medicine_id', 'scheduled_at']);
            $table->dropColumn('scheduled_at');
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->date('scheduled_date')->after('medicine_id');
            $table->dateTime('scheduled_end_at')->after('scheduled_date');
        });

        Schema::table('medicine_intake_logs', function (Blueprint $table) {
            $table->foreign('medicine_id')->references('id')->on('medicines')->cascadeOnDelete();
            $table->unique(['medicine_id', 'scheduled_date']);
        });
    }
};
