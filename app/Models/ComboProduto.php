<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProduto extends Model
{
    use HasFactory;

    protected $table = 'combo_produtos';

    protected $fillable = [
        'combo_id',
        'produto_id',
        'quantidade'
    ];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
