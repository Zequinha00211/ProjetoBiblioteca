<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Rotas abertas, sem middleware de autenticação
Route::prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/update', [AuthController::class, 'update']);
});

Route::group(['prefix' => "/autor"], function () {
    Route::post('/save-autor', [AutorController::class, 'criarAutor']);
    Route::get('/listar-autores', [AutorController::class, 'todosAutores']);
    Route::put('/atualizar-autor', [AutorController::class, 'atualizarAutor']);
    Route::delete('/deletar-autor/{id}', [AutorController::class, 'deletarAutor']);
});

Route::group(['prefix' => "/livro"], function () {
    Route::post('/save-livro', [LivroController::class, 'criarLivro']);
    Route::get('/listar-livros', [LivroController::class, 'todosLivros']);
    Route::put('/atualizar-livro', [LivroController::class, 'atualizarLivro']);
    Route::delete('/deletar-livro/{id}', [LivroController::class, 'deletarLivro']);
    Route::get('/listar-generos', [LivroController::class, 'todosGeneros']);
    Route::get('/listar-editoras', [LivroController::class, 'todasEditoras']);
    Route::put('/emprestar-livro', [LivroController::class, 'emprestarLivro']);
});

Route::group(['prefix' => 'usuarios'], function () {
    Route::get('/', [UserController::class, 'todosUsuarios']);
    Route::put('/', [UserController::class, 'update']);
    Route::post('/{idUser}/atualiza-foto', [UserController::class, 'atualizarFotoUsuario']);
    Route::get('/{idUser}', [UserController::class, 'buscarUsuario']);
    Route::delete('/delete/{id}', [UserController::class, 'deletarUsuario']);
    Route::get('/buscar/privilegios', [UserController::class, 'buscarPrivilegios']);
    Route::put('/atualizar-proprio-usuario', [UserController::class, 'atualizarProprioUser']);
});
