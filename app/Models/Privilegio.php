<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    protected $table = "tblprivilegios";
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'mysql';
    protected $fillable = [
        'ID',
        'NOME',
        'DESCRICAO'
    ];
}
