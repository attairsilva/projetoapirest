<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; #Seeders

class Lotacao extends Model
{
    use HasFactory; //Seeders

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unid_id', 'unid_id');
    }


    protected $table = 'lotacao';
    protected $primaryKey = 'lot_id'; // chave primária
    protected $keyType = 'integer'; // inteiro
    public $timestamps = false;
    protected $fillable = [
       'pes_id',
       'unid_id',
       'lot_data_lotacao',
       'lot_data_remocao',
       'lot_portaria'
    ];
}
