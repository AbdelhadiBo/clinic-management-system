<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facture', function (Blueprint $table) {
            $table->id('id_facture');
            $table->unsignedBigInteger('id_consultation');
            $table->date('date');
            $table->decimal('montant_total', 10, 2);
            $table->string('statut_paiement', 50)->default('non payé');
            $table->string('mode_paiement', 50)->nullable();

            // Clé étrangère
            $table->foreign('id_consultation')
                ->references('id_consultation')
                ->on('consultation')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture');
    }
};
