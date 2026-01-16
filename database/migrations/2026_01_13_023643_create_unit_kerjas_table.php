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
        Schema::create('unit_kerjas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 5)->nullable();
            $table->string('nama', 50)->nullable();
            $table->uuid('treecode')->nullable();
            $table->uuid('parentcode')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('status')->nullable();
            $table->string('singkatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_kerjas');
    }
};
