<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation', function (Blueprint $table) {
            $table->id('id_consultation');
            $table->unsignedBigInteger('id_rdv');
            $table->unsignedBigInteger('id_medecin');
            $table->unsignedBigInteger('id_infirmier')->nullable();
            $table->date('date');
            $table->text('diagnostic')->nullable();
            $table->text('traitement')->nullable();
            $table->text('observations')->nullable();

            // Clés étrangères
            $table->foreign('id_rdv')->references('id_rdv')->on('rendez_vous')->onDelete('cascade');
            $table->foreign('id_medecin')->references('id_medecin')->on('medecin')->onDelete('cascade');
            $table->foreign('id_infirmier')->references('id_infirmier')->on('infirmier')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation');
    }
};
