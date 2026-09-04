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
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('empresa');
            $table->string('cargo');
            $table->date('fecha_postulacion');
            $table->enum('estado', ['Postulado', 'Entrevista', 'Oferta', 'Rechazado'])->default('Postulado');
            $table->string('link_vacante')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
