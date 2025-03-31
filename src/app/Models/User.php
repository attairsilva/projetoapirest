<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public function setRememberToken($value)
    {
        // Deixe vazio para evitar erro
    }

    public function getRememberTokenName()
    {
        return null; // Não usa remember_token
    }

    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'token_expires_at',
    ];

    protected $hidden = [
        'password',
    ];

     protected $casts = [
         //     'email_verified_at' => 'datetime',
     ];
}
