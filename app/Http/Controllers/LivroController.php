<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use App\Models\Genero;
use App\Models\Livro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LivroController
{
    public function criarLivro(Request $request)
    {
        try {
            $validator = \Validator::make($request->json()->all(), [
                'NOMELIVRO' => 'required',
                'IDAUTOR' => 'required',
                'NUMEROREGISTRO' => 'required',
                'SITUACAOLIVRO' => 'required',
            ]);

            if ($validator->fails()) {
                return \Response::json([
                    'erros' => (array)$validator->messages()
                ], 400);
            }
            for ($i = 0; $i < (int)$request->QTDLIVROS; $i++) {
                $livro = new Livro();
                $livro->NOMELIVRO       = $request->NOMELIVRO;
                $livro->IDAUTOR         = $request->IDAUTOR;
                $livro->NUMEROREGISTRO  = $request->NUMEROREGISTRO;
                $livro->SITUACAOLIVRO   = $request->SITUACAOLIVRO;
                $livro->IDGENERO        = isset($request->IDGENERO) ? $request->IDGENERO : null;
                $livro->EDITORA         = isset($request->EDITORA) ? $request->EDITORA : null;

                $livro->save();
            }
            return response()->json(['sucesso' => true, 'data' => $livro]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function atualizarLivro(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'NOMELIVRO' => 'required',
                'IDAUTOR' => 'required',
                'NUMEROREGISTRO' => 'required',
                'SITUACAOLIVRO' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['sucesso' => false, 'erros' => (array)$validator->errors()->all()], 400);
            }

            $livro = Livro::where('ID', $request->ID)->first();

            if (!empty($livro)) {
                $livro->NOMELIVRO       = $request->NOMELIVRO;
                $livro->IDAUTOR         = $request->IDAUTOR;
                $livro->NUMEROREGISTRO  = $request->NUMEROREGISTRO;
                $livro->SITUACAOLIVRO   = $request->SITUACAOLIVRO;
                $livro->IDGENERO        = $request->IDGENERO;
                $livro->EDITORA         = $request->EDITORA;
                $livro->update();
            }

            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function todosLivros(Request $request)
    {
        try {
            $livros = Livro::select('tbllivros.*', 'tblautor.NOME as NOMEAUTOR', 'tblgenero.GENERO as NOMEGENERO')
                ->join('tblautor', 'tblautor.ID', 'tbllivros.IDAUTOR')
                ->join('tblgenero', 'tblgenero.ID', 'tbllivros.IDGENERO')
                ->porFiltro($request->all())
                ->get();

            foreach ($livros as $livro) {
                if ($livro->SITUACAOLIVRO == 0) {
                    $livro->SITUACAO = 'DISPONÍVEL';
                } else if ($livro->SITUACAOLIVRO == 1) {
                    $livro->SITUACAO = 'EMPRESTADO';
                } else {
                    $livro->SITUACAO = 'ATRASADO';
                }
            }
            return response()->json(['sucesso' => true, 'data' => $livros]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function deletarLivro($idLivro)
    {
        try {
            Livro::where('ID', $idLivro)->delete();
            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function todosGeneros()
    {
        try {
            $generos = Genero::get();

            return response()->json(['sucesso' => true, 'data' => $generos]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function todasEditoras()
    {
        try {
            $editoras = Editora::get();

            return response()->json(['sucesso' => true, 'data' => $editoras]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function emprestarLivro(Request $request)
    {
        try {

            $livro = Livro::where('ID', $request->ID)->first();

            if (!empty($livro) && $request->SITUACAOLIVRO == 0) {
                $livro->SITUACAOLIVRO   = 1;
                $livro->IDUSUARIO       = $request->IDUSUARIO;
                $livro->DATADEVOLUCAO   = Carbon::now()->addDays(7)->toDateString();
                $livro->update();
            } else {
                $livro->SITUACAOLIVRO   = 0;
                $livro->IDUSUARIO       = null;
                $livro->DATADEVOLUCAO   = null;
                $livro->update();
            }

            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }
}
