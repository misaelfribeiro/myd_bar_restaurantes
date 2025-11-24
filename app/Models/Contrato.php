<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Contrato extends Model
{
 use SoftDeletes;
 protected $fillable = [
 'empresa_id',
 'plano_id',
 'numero_contrato',
 'data_inicio',
 'data_fim',
 'data_renovacao',
 'tipo_pagamento',
 'valor_contratado',
 'desconto_aplicado',
 'valor_final',
 'max_usuarios',
 'max_produtos',
 'max_pedidos_mes',
 'max_filiais',
 'status',
 'observacoes',
 'documento_assinado',
 'documento_identidade',
 'comprovante_endereco',
 'data_cancelamento',
 'motivo_cancelamento',
 'cancelado_por',
 'criado_por',
 'aprovado_por',
 'aprovado_em',
 ];
 protected $casts = [
 'data_inicio' => 'date',
 'data_fim' => 'date',
 'data_renovacao' => 'date',
 'data_cancelamento' => 'date',
 'aprovado_em' => 'datetime',
 'valor_contratado' => 'decimal:2',
 'desconto_aplicado' => 'decimal:2',
 'valor_final' => 'decimal:2',
 ];
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public function plano()
 {
 return $this->belongsTo(Plano::class);
 }
 public function criador()
 {
 return $this->belongsTo(User::class, 'criado_por');
 }
 public function aprovador()
 {
 return $this->belongsTo(User::class, 'aprovado_por');
 }
 public function historico()
 {
 return $this->hasMany(HistoricoContrato::class)->orderBy('created_at', 'desc');
 }
 public function faturas()
 {
 return $this->hasMany(Fatura::class);
 }
 public static function gerarNumeroContrato()
 {
 $ano = date('Y');
 $ultimo = static::where('numero_contrato', 'like', "CTR-{$ano}-%")->count();
 return sprintf('CTR-%s-%05d', $ano, $ultimo + 1);
 }
 public function proximoVencimento($dias = 30)
 {
 return $this->data_fim->diffInDays(now(), false) >= -$dias;
 }
 public function isVencido()
 {
 return $this->data_fim->isPast() && $this->status === 'ativo';
 }
 public function diasAteVencimento()
 {
 return $this->data_fim->diffInDays(now(), false);
 }
 public function scopeAtivo($query)
 {
 return $query->where('status', 'ativo');
 }
 public function scopeVencendo($query, $dias = 30)
 {
 return $query->where('status', 'ativo')
 ->whereRaw('DATEDIFF(data_fim, CURDATE()) <= ?', [$dias])
 ->whereRaw('DATEDIFF(data_fim, CURDATE()) >= 0');
 }
}