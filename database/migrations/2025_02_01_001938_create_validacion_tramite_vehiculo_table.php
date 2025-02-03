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
        Schema::create('validacion_tramite_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->string('serie')->nullable();
            $table->string('folio')->nullable();
            $table->string('encriptado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validacion_tramite_vehiculo');
    }
};
