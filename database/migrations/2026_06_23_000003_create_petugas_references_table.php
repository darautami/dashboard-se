<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas_references', function (Blueprint $table) {
            $table->id();
            $table->string('petugas_email')->unique();
            $table->string('nama_petugas')->nullable();
            $table->string('kode_kecamatan')->nullable();
            $table->string('nama_kecamatan')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas_references');
    }
};
