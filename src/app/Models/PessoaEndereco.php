<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PessoaEndereco extends Model
{

    protected $table = 'pessoa_endereco';

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    protected $primaryKey = 'pes_id'; // chave primária
    protected $keyType = 'integer'; // inteiro
    public $timestamps = false;
    protected $fillable = [
        'pes_id',
        'end_id'
    ];


}
