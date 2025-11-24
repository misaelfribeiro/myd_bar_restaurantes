<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Fatura extends Model
{
 protected $fillable = [
 'contrato_id',
 'empresa_id',
 'numero_fatura',
 'data_referencia',
 'data_vencimento',
 'data_emissao',
 'valor_plano',
 'valor_adicional',
 'desconto',
 'valor_total',
 'itens_adicionais',
 'status',
 'data_pagamento',
 'forma_pagamento',
 'comprovante_pagamento',
 'chave_nfe',
 'numero_nfe',
 'arquivo_nfe',
 'observacoes',
 ];
 protected $casts = [
 'data_referencia' => 'date',
 'data_vencimento' => 'date',
 'data_emissao' => 'date',
 'data_pagamento' => 'date',
 'valor_plano' => 'decimal:2',
 'valor_adicional' => 'decimal:2',
 'desconto' => 'decimal:2',
 'valor_total' => 'decimal:2',
 'itens_adicionais' => 'array',
 ];
 public function contrato()
 {
 return $this->belongsTo(Contrato::class);
 }
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public static function gerarNumeroFatura()
 {
 $ano = date('Y');
 $mes = date('m');
 $ultimo = static::where('numero_fatura', 'like', "FAT-{$ano}{$mes}-%")->count();
 return sprintf('FAT-%s%s-%05d', $ano, $mes, $ultimo + 1);
 }
 public function isVencida()
 {
 return $this->status === 'pendente' && $this->data_vencimento->isPast();
 }
 public function diasAteVencimento()
 {
 return $this->data_vencimento->diffInDays(now(), false);
 }
 public function marcarComoPaga($formaPagamento = null, $comprovante = null)
 {
 $this->update([
 'status' => 'pago',
 'data_pagamento' => now(),
 'forma_pagamento' => $formaPagamento,
 'comprovante_pagamento' => $comprovante,
 ]);
 }
 public function scopePendente($query)
 {
 return $query->where('status', 'pendente');
 }
 public function scopeVencida($query)
 {
 return $query->where('status', 'pendente')
 ->where('data_vencimento', '<', now());
 }
}