<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; #Seeders

class Cidade extends Model
{
    use HasFactory; //Seeders
    protected $table = 'cidade';
    protected $primaryKey = 'cid_id'; // chave primária
    protected $keyType = 'integer'; // inteiro
    public $timestamps = false;
    protected $fillable = [
        'cid_nome', 'cid_uf'
    ];

}
