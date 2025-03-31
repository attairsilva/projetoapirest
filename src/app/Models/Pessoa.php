<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; #Seeders
use Illuminate\Database\Eloquent\Model;


class Pessoa extends Model
{

    use HasFactory; #Seeders

    protected $table = 'pessoa';


    public function fotoPessoa()
    {
        return $this->hasMany(FotoPessoa::class, 'pes_id', 'pes_id');
    }

    protected $primaryKey = 'pes_id'; // chave primária

    public function enderecos()
    {
        return $this->hasMany(PessoaEndereco::class, 'pes_id', 'pes_id');
    }

    public function endereco()
    {
        // return $this->belongsToMany(
        //     Endereco::class,       // Modelo relacionado
        //     'pessoa_endereco',     // tabela de relacionamento
        //     'pes_id',              // Chave estrangeira
        //     'end_id'               // Chave estrangeira
        // );
        return $this->belongsToMany(
            Endereco::class,
            'pessoa_endereco',
            'pes_id',
            'end_id')
        ->withPivot('pes_id', 'end_id');
    }



    public $timestamps = false;
    protected $fillable = [
        'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
    ];
}
