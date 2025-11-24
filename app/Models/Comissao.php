<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Comissao extends Model
{
 use HasFactory, BelongsToTenant;
 protected $table = 'comissoes';
 protected $fillable = [
 'funcionario_id',
 'tenant_code',
 'pedido_id',
 'tipo',
 'descricao',
 'data_referencia',
 'valor_base',
 'percentual',
 'valor_comissao',
 'status',
 'data_pagamento',
 'observacoes'
 ];
 protected $casts = [
 'data_referencia' => 'date',
 'data_pagamento' => 'date',
 'valor_base' => 'decimal:2',
 'percentual' => 'decimal:2',
 'valor_comissao' => 'decimal:2',
 ];
 public function funcionario()
 {
 return $this->belongsTo(Funcionario::class);
 }
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
}