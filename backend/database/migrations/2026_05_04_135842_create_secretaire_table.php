<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secretaire', function (Blueprint $table) {
            $table->id('id_secretaire');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('telephone', 20);
            $table->string('email', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretaire');
    }
};
