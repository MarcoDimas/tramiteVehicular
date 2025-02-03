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
        Schema::create('_bitacora', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->nullable();
            $table->string('datoEnviado')->nullable();
            $table->string('datoRespuesta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_bitacora');
    }
};
