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
        Schema::create('tbllivros', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('NOMELIVRO');
            $table->unsignedInteger('IDAUTOR');
            $table->string('NUMEROREGISTRO');
            $table->boolean('SITUACAOLIVRO');
            $table->unsignedInteger('IDGENERO')->nullable();
            $table->string('EDITORA')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbllivros');
    }
};
