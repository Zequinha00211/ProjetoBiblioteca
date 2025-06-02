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
        Schema::create('tblprivilegios', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('NOME');
            $table->string('DESCRICAO')->nullable();
            $table->timestamps();
        });

        DB::table('tblprivilegios')->insert([
            ['NOME' => 'admin', 'DESCRICAO' => 'Administrador'],
            ['NOME' => 'usuario', 'DESCRICAO' =>'Usuário comum'] ,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblprivilegios');
    }
};
