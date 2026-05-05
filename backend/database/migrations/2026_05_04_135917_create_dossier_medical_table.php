<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossier_medical', function (Blueprint $table) {
            $table->id('id_dossier');
            $table->unsignedBigInteger('id_patient');
            $table->date('date_creation');
            $table->text('antecedents')->nullable();
            $table->text('allergies')->nullable();

            // Clé étrangère
            $table->foreign('id_patient')
                ->references('id_patient')
                ->on('patient')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_medical');
    }
};
