<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Pedido extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'mesa_id',
 'usuario_id',
 'cliente_id',
 'total',
 'status',
 'forma_pagamento',
 'observacoes',
 'entregador_id',
 'saiu_entrega',
 'entregue_em',
 'taxa_entrega',
 'observacoes_entrega',
 'tenant_code',
 ];
 public function mesa()
 {
 return $this->belongsTo(Mesa::class);
 }
 public function usuario()
 {
 return $this->belongsTo(Usuario::class);
 }
 public function itens()
 {
 return $this->hasMany(ItemPedido::class);
 }
 public function pagamentos()
 {
 return $this->hasMany(Pagamento::class);
 }
 public function delivery()
 {
 return $this->hasOne(Delivery::class);
 }
 public function entregador()
 {
 return $this->belongsTo(Entregador::class);
 }
 public function cliente()
 {
 return $this->belongsTo(Cliente::class);
 }
 public function avaliacao()
 {
 return $this->hasOne(AvaliacaoEntregador::class);
 }
 public function getTotalPagoAttribute()
 {
 return $this->pagamentos()->where('status', 'confirmado')->sum('valor');
 }
 public function getSaldoRestanteAttribute()
 {
 return $this->total - $this->total_pago;
 }
 public function isPago()
 {
 return $this->saldo_restante <= 0;
 }
 protected $casts = [
 'saiu_entrega' => 'datetime',
 'entregue_em' => 'datetime',
 'taxa_entrega' => 'decimal:2',
 ];
 protected $appends = [
 'entregador_nome',
 'entregador_veiculo'
 ];
 public function getEntregadorNomeAttribute()
 {
 if ($this->relationLoaded('entregador') && $this->getRelation('entregador')) {
 return $this->getRelation('entregador')->nome;
 }
 return null;
 }
 public function getEntregadorVeiculoAttribute()
 {
 if ($this->relationLoaded('entregador') && $this->getRelation('entregador')) {
 return $this->getRelation('entregador')->tipo_veiculo;
 }
 return null;
 }
 public function temEntregador()
 {
 return !is_null($this->entregador_id);
 }
 public function saiuParaEntrega()
 {
 return !is_null($this->saiu_entrega);
 }
 public function foiEntregue()
 {
 return !is_null($this->entregue_em);
 }
 public function podeSerEntregue()
 {
 return $this->status === 'pronto' && $this->delivery;
 }
 public function atribuirEntregador($entregadorId)
 {
 $this->update(['entregador_id' => $entregadorId]);
 }
 public function marcarSaiuEntrega()
 {
 $this->update([
 'saiu_entrega' => now(),
 'status' => 'saiu_entrega'
 ]);
 }
 public function marcarEntregue()
 {
 $this->update([
 'entregue_em' => now(),
 'status' => 'entregue'
 ]);
 if ($this->entregador) {
 $this->entregador->atualizarEstatisticas();
 }
 }
}