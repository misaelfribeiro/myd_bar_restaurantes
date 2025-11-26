<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'numero_pedido',
        'tenant_code',
        'mp_payment_id',
        'mp_preference_id',
        'payment_method',
        'status',
        'amount',
        'platform_fee',
        'gateway_fee',
        'delivery_fee',
        'net_amount',
        'pix_qr_code',
        'pix_qr_code_url',
        'pix_copy_paste',
        'paid_at',
        'expires_at',
        'mp_response',
        'refund_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relacionamento com pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Verifica se pagamento está aprovado
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Verifica se pagamento está pendente
    public function isPending()
    {
        return $this->status === 'pending' || $this->status === 'in_process';
    }

    // Verifica se pagamento expirou
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    // Marca como pago
    public function markAsPaid()
    {
        $this->update([
            'status' => 'approved',
            'paid_at' => now()
        ]);
    }

    // Marca como cancelado
    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'refund_reason' => $reason
        ]);
    }
}
