<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gps_data', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index(); // ID unik sensor / perangkat tracking
            $table->decimal('latitude', 10, 8);   // Koordinat Garis Lintang (-90 s/d 90)
            $table->decimal('longitude', 11, 8);  // Koordinat Garis Bujur (-180 s/d 180)
            $table->float('altitude')->nullable(); // Ketinggian (meter)
            $table->float('speed')->nullable();    // Kecepatan (km/jam)
            $table->integer('satellites')->nullable(); // Jumlah satelit terkunci
            $table->timestamp('recorded_at')->nullable(); // Waktu baca GPS dari modul
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps_data');
    }
};
