<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = "tblgenero";
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'mysql';
    protected $fillable = [
        'ID',
        'GENERO',
    ];
}
