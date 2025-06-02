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
        Schema::create('tbleditora', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('NOMEEDITORA');
            $table->timestamps();
        });

        DB::table('tbleditora')->insert([
        ['NOMEEDITORA' => 'Editora Aurora'],
        ['NOMEEDITORA' => 'Letras & Mundos'],
        ['NOMEEDITORA' => 'Papel Encantado'],
        ['NOMEEDITORA' => 'Sabedoria Press'],
        ['NOMEEDITORA' => 'Livros do Horizonte'],
        ['NOMEEDITORA' => 'Editora Prisma'],
        ['NOMEEDITORA' => 'Universo Editorial'],
        ['NOMEEDITORA' => 'Fábulas & Cia'],
        ['NOMEEDITORA' => 'Editora Infinita'],
        ['NOMEEDITORA' => 'Narrativa Nova'],
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbleditora');
    }
};
