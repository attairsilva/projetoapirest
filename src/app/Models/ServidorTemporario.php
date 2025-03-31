<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model
{

    # um servidor efetivo  muitas pessoas
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    # um servidor efetivo muitas lotacoes
    public function lotacao()
    {
        return $this->belongsTo(Lotacao::class, 'pes_id', 'pes_id');
    }

    # um servidor varias
    public function foto()
    {
        return $this->hasMany(FotoPessoa::class, 'pes_id', 'pes_id');
    }


    protected $table = 'servidor_temporario'; # tabela
    protected $primaryKey = 'pes_id'; # chave primária
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
       'pes_id', 'st_data_admissao', 'st_data_demissao'
    ];
}
