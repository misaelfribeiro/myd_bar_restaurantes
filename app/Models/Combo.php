<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Combo extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'nome',
        'descricao',
        'preco_original',
        'preco_combo',
        'imagem',
        'ativo',
        'destaque',
        'tenant_code'
    ];

    protected $casts = [
        'preco_original' => 'decimal:2',
        'preco_combo' => 'decimal:2',
        'ativo' => 'boolean',
        'destaque' => 'boolean'
    ];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'combo_produtos')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }

    public function comboProdutos()
    {
        return $this->hasMany(ComboProduto::class);
    }
    
    public function tenant()
    {
        return $this->belongsTo(Empresa::class, 'tenant_code', 'tenant_code');
    }

    public function getDescontoAttribute()
    {
        if ($this->preco_original > 0) {
            return round((($this->preco_original - $this->preco_combo) / $this->preco_original) * 100, 0);
        }
        return 0;
    }

    public function getEconomiaAttribute()
    {
        return $this->preco_original - $this->preco_combo;
    }
}
