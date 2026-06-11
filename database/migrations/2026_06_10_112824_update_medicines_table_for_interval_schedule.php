<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->unsignedTinyInteger('interval_hours')->after('route');
            $table->unsignedTinyInteger('times_per_day')->after('interval_hours');
            $table->dateTime('first_dose_at')->after('times_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['interval_hours', 'times_per_day', 'first_dose_at']);
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->time('start_time')->after('route');
            $table->time('end_time')->after('start_time');
        });
    }
};
