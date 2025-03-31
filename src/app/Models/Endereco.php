<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; #Seeders

class Endereco extends Model
{
    use HasFactory; #Seeders

    protected $table = 'endereco';
    protected $primaryKey = 'end_id'; // chave primária

    public function pessoas()
    {
        return $this->belongsToMany(
            Pessoa::class,
            'pessoa_endereco',
            'end_id',
            'pes_id'
        );
    }


    public function unidades()
    {
        return $this->belongsToMany(Unidade::class, 'unidade_endereco', 'end_id', 'unid_id');
    }

    public $timestamps = false;
    protected $fillable = [
        'end_tipo_logradouro',
        'end_logradouro',
        'end_numero',
        'end_bairro',
        'cid_id'
    ];



}
