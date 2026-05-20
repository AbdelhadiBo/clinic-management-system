<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medecin', function (Blueprint $table) {
            $table->id('id_medecin');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('specialite', 100);
            $table->string('telephone', 20);
            $table->string('email', 100)->nullable();
            $table->string('matricule', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medecin');
    }
};
