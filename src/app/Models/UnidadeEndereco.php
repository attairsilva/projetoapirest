<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model
{   public $timestamps = false;
    protected $table = 'unidade_endereco';
    protected $primaryKey = 'unid_id';
    protected $keyType = 'integer'; # inteiro
    protected $fillable = [
        'unid_id',
        'end_id'
    ];


}
