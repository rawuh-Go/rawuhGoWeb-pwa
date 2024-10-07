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
        Schema::table('leaves', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi datetime untuk menyimpan tanggal dan waktu
            $table->datetime('tanggal')->default(DB::raw('CURRENT_TIMESTAMP'))->after('user_id');

            $table->enum('type_leave', ['Sick Leave', 'Personal Leave', 'Marriage Leave', 'Annual Leave'])
                ->default('Personal Leave')->after('tanggal_selesai');

            $table->string('attachment')->after('type_leave');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            //
        });
    }
};
