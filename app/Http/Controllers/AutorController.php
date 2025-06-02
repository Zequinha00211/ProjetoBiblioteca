<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Http\Request;

class AutorController
{
    public function criarAutor(Request $request)
    {
        try {
            $validator = \Validator::make($request->json()->all(), [
                'NOME' => 'required'
            ]);

            if ($validator->fails()) {
                return \Response::json([
                    'erros' => (array)$validator->messages()
                ], 400);
            }

            $autor = new Autor();
            $autor->NOME = $request->NOME;

            $autor->save();
            return response()->json(['sucesso' => true, 'data' => $autor]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function atualizarAutor(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'NOME' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['sucesso' => false, 'erros' => (array)$validator->errors()->all()], 400);
            }

            $autor = Autor::where('ID', $request->ID)->first();

            if (!empty($autor)) {
                $autor->NOME = $request->NOME;
                $autor->update();
            }

            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function todosAutores()
    {
        try {
            $autores = Autor::get();

            return response()->json(['sucesso' => true, 'data' => $autores]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function deletarAutor($idAutor)
    {
        try {
            $livros = Livro::where('IDAUTOR', $idAutor)->count();
            if ($livros > 0) {
                return response()->json([
                    'sucesso' => false,
                    'erros' => ['Este autor possui livros vinculados e não pode ser excluído.']
                ], 400);
            }

            Autor::where('ID', $idAutor)->delete();
            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }
}
