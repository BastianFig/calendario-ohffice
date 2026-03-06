<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'usuario_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'descripcion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
