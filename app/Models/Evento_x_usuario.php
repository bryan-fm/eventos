<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Evento;

class Evento_x_usuario extends Model
{
    public $timestamps = true;
    protected $table = 'evento_x_usuario';

    public function convidado() {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function evento() {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

}