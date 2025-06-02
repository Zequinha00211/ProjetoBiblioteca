<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    protected $table = "tbllivros";
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'mysql';
    protected $fillable = [
        'ID',
        'NOMELIVRO',
        'IDAUTOR',
        'NUMEROREGISTRO',
        'SITUACAOLIVRO',
        'IDGENERO',
        'EDITORA',
        'DATADEVOLUCAO'
    ];

    public function scopePorFiltro($query, $filtros)
    {
        if (isset($filtros['IDAUTOR']) && $filtros['IDAUTOR'] != '') {
            $query->where('IDAUTOR', $filtros['IDAUTOR']);
        }

        if (isset($filtros['IDGENERO']) && $filtros['IDGENERO'] != '') {
            $query->where('IDGENERO', $filtros['IDGENERO']);
        }

        if (isset($filtros['EDITORA']) && $filtros['EDITORA'] != '') {
            $query->where('EDITORA', $filtros['EDITORA']);
        }

        return $query;
    }
}
