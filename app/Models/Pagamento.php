<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Pagamento extends Model
{
 use HasFactory;
 protected $fillable = [
 'pedido_id',
 'caixa_id',
 'forma_pagamento',
 'valor',
 'valor_recebido',
 'troco',
 'status',
 'observacoes',
 'usuario_id',
 'data_pagamento'
 ];
 protected $dates = [
 'data_pagamento'
 ];
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
 public function usuario()
 {
 return $this->belongsTo(Usuario::class);
 }
 public function caixa()
 {
 return $this->belongsTo(Caixa::class);
 }
 public function scopePorData($query, $data)
 {
 return $query->whereDate('created_at', $data);
 }
 public function scopePorFormaPagamento($query, $forma)
 {
 return $query->where('forma_pagamento', $forma);
 }
 public static function formasPagamento()
 {
 return [
 'dinheiro' => 'Dinheiro',
 'cartao_credito' => 'Cartão de Crédito',
 'cartao_debito' => 'Cartão de Débito',
 'pix' => 'PIX',
 'vale_refeicao' => 'Vale Refeição'
 ];
 }
 public function getFormaPagamentoNomeAttribute()
 {
 return self::formasPagamento()[$this->forma_pagamento] ?? $this->forma_pagamento;
 }
}