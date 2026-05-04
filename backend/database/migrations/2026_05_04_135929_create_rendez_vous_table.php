<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id('id_rdv');
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_medecin');
            $table->unsignedBigInteger('id_secretaire')->nullable();
            $table->date('date_rdv');
            $table->time('heure');
            $table->text('motif')->nullable();
            $table->string('statut', 50)->default('en attente');

            // Clés étrangères
            $table->foreign('id_patient')->references('id_patient')->on('patient')->onDelete('cascade');
            $table->foreign('id_medecin')->references('id_medecin')->on('medecin')->onDelete('cascade');
            $table->foreign('id_secretaire')->references('id_secretaire')->on('secretaire')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
