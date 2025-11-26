<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estorno extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'item_pedido_id',
        'tipo',
        'valor',
        'motivo',
        'solicitado_por',
        'aprovado_por',
        'status',
        'solicitado_em',
        'processado_em',
        'observacoes_aprovacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'solicitado_em' => 'datetime',
        'processado_em' => 'datetime',
    ];

    /**
     * Relacionamento com pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relacionamento com item do pedido
     */
    public function itemPedido()
    {
        return $this->belongsTo(ItemPedido::class, 'item_pedido_id');
    }

    /**
     * Usuário que solicitou o estorno
     */
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    /**
     * Usuário que aprovou/rejeitou o estorno
     */
    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pendente' => 'warning',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute()
    {
        $icons = [
            'pendente' => 'fa-clock',
            'aprovado' => 'fa-check-circle',
            'rejeitado' => 'fa-times-circle',
        ];
        return $icons[$this->status] ?? 'fa-question-circle';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
        ];
        return $texts[$this->status] ?? 'Desconhecido';
    }
}
