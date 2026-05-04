<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordonnance', function (Blueprint $table) {
            $table->id('id_ordonnance');
            $table->unsignedBigInteger('id_consultation');
            $table->date('date');
            $table->text('medicaments');
            $table->text('posologie');

            // Clé étrangère
            $table->foreign('id_consultation')
                ->references('id_consultation')
                ->on('consultation')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordonnance');
    }
};
