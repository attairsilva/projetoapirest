<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; #Seeders

class Unidade extends Model
{

    use HasFactory; #Seeders

    # relacionamento UnidadeEndereço
    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unid_id', 'end_id');
    }




    protected $table = 'unidade'; # tabela
    protected $primaryKey = 'unid_id';
    protected $keyType = 'integer'; # inteiro
    public $timestamps = false;

    protected $fillable = [
       'unid_nome', 'unid_sigla'
    ];
}
