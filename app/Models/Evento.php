<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Evento extends Model
{
    public $timestamps = true;
    protected $table = 'eventos';
    protected $dates = ['data'];

    public function organizador() {
        return $this->belongsTo(User::class, 'organizador_id');
    }


}