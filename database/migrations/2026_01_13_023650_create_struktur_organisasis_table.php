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
        Schema::create('struktur_organisasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jabatan', 5)->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->uuid('treecode')->nullable();
            $table->uuid('parentcode')->nullable();
            $table->boolean('approval')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('struktural')->nullable();
            $table->boolean('fungsional')->nullable();
            $table->boolean('is_puk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struktur_organisasis');
    }
};
