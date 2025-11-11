<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'identificador',
        'lugares',
        'numero',
        'capacidade',
        'status'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
