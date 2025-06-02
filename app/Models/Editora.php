<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editora extends Model
{
    protected $table = "tbleditora";
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $connection = 'mysql';
    protected $fillable = [
        'ID',
        'NOMEEDITORA',
    ];
}
