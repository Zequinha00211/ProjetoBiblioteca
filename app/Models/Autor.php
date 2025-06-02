<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = "tblautor";
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'mysql';
    protected $fillable = [
        'ID',
        'NOME',
    ];
}
