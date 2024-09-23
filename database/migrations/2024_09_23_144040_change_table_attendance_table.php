<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
            $table->renameColumn('latitude', 'datang_latitude');
            $table->renameColumn('longitude', 'datang_longitude');
            $table->double('pulang_latitude')->nullable();
            $table->double('pulang_longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
            $table->renameColumn('datang_latitude', 'latitude');
            $table->renameColumn('datang_longitude', 'longitude');
            $table->dropColumn('pulang_latitude');
            $table->dropColumn('pulang_longitude');
        });
    }
};
