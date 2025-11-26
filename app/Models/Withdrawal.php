<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_code',
        'valor',
        'status',
        'observacao',
        'metodo_pagamento',
        'dados_bancarios',
        'aprovado_por',
        'data_solicitacao',
        'data_aprovacao',
        'data_processamento',
        'comprovante'
    ];

    protected $casts = [
        'dados_bancarios' => 'array',
        'data_solicitacao' => 'datetime',
        'data_aprovacao' => 'datetime',
        'data_processamento' => 'datetime',
        'valor' => 'decimal:2'
    ];

    // Relacionamento com Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'tenant_code', 'codigo');
    }

    // Relacionamento com o admin que aprovou
    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }
}

