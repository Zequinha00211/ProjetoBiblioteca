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
        Schema::create('tblgenero', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('GENERO');
            $table->timestamps();
        });

        DB::table('tblgenero')->insert([
            ['GENERO' => 'Ação',                'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Aventura',            'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Comédia',             'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Drama',               'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Ficção Científica',   'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Romance',             'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Suspense',            'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Terror',              'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Fantasia',            'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Animação',            'created_at' => now(), 'updated_at' => now()],
            ['GENERO' => 'Documentário',        'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblgenero');
    }
};
