<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;
class Caixa extends Model
{
 use HasFactory, BelongsToTenant;
 protected $table = 'caixa';
 protected $fillable = [
 'usuario_id',
 'usuario_fechamento_id',
 'saldo_inicial',
 'saldo_final',
 'total_vendas',
 'total_dinheiro',
 'total_cartao_credito',
 'total_cartao_debito',
 'total_cartao',
 'total_pix',
 'total_vale',
 'data_abertura',
 'data_fechamento',
 'status',
 'observacoes_abertura',
 'tenant_code',
 'observacoes_fechamento'
 ];
 protected $dates = [
 'data_abertura',
 'data_fechamento'
 ];
 public function usuario()
 {
 return $this->belongsTo(Usuario::class);
 }
 public function pagamentos()
 {
 return $this->hasMany(Pagamento::class)
 ->where('status', 'confirmado');
 }
 public function pagamentosEspecificos()
 {
 $query = Pagamento::where('status', 'confirmado')
 ->where('caixa_id', $this->id);
 return $query;
 }
 public function scopeAberto($query)
 {
 return $query->where('status', 'aberto');
 }
 public function scopeFechado($query)
 {
 return $query->where('status', 'fechado');
 }
 public function scopePorData($query, $data)
 {
 return $query->whereDate('data_abertura', $data);
 }
 public static function caixaAbertoHoje()
 {
 return self::where('status', 'aberto')
 ->orderBy('data_abertura', 'desc')
 ->first();
 }
 public function calcularTotais()
 {
 $pagamentos = $this->pagamentosEspecificos()->get();
 $this->total_vendas = $pagamentos->sum('valor');
 $this->total_dinheiro = $pagamentos->where('forma_pagamento', 'dinheiro')->sum('valor');
 $this->total_cartao_credito = $pagamentos->where('forma_pagamento', 'cartao_credito')->sum('valor');
 $this->total_cartao_debito = $pagamentos->where('forma_pagamento', 'cartao_debito')->sum('valor');
 $this->total_cartao = $this->total_cartao_credito + $this->total_cartao_debito;
 $this->total_pix = $pagamentos->where('forma_pagamento', 'pix')->sum('valor');
 $this->total_vale = $pagamentos->whereIn('forma_pagamento', ['vale_refeicao', 'vale'])->sum('valor');
 $this->saldo_final = ($this->saldo_inicial ?? 0) + $this->total_dinheiro;
 return $this;
 }
 public function getTotalizacoes()
 {
 $pagamentos = $this->pagamentosEspecificos()->get();
 return [
 'total_vendas' => $pagamentos->sum('valor'),
 'total_recebido' => $pagamentos->sum('valor_recebido'),
 'total_troco' => $pagamentos->sum('troco'),
 'quantidade_vendas' => $pagamentos->count(),
 'por_forma_pagamento' => $pagamentos->groupBy('forma_pagamento')
 ->map(function ($pagamentosForma, $forma) {
 return [
 'forma' => $forma,
 'quantidade' => $pagamentosForma->count(),
 'total' => $pagamentosForma->sum('valor'),
 'recebido' => $pagamentosForma->sum('valor_recebido'),
 'troco' => $pagamentosForma->sum('troco')
 ];
 })
 ];
 }
 public function getTotalizacoesPorPeriodo()
 {
 $pagamentos = $this->getPagamentosDoPeriodo();
 return [
 'total_vendas' => $pagamentos->sum('valor'),
 'total_recebido' => $pagamentos->sum('valor_recebido'),
 'total_troco' => $pagamentos->sum('troco'),
 'quantidade_vendas' => $pagamentos->count(),
 'por_forma_pagamento' => $pagamentos->groupBy('forma_pagamento')
 ->map(function ($pagamentosForma, $forma) {
 return [
 'forma' => $forma,
 'quantidade' => $pagamentosForma->count(),
 'total' => $pagamentosForma->sum('valor'),
 'recebido' => $pagamentosForma->sum('valor_recebido'),
 'troco' => $pagamentosForma->sum('troco')
 ];
 })
 ];
 }
 public function getPagamentosDoPeriodo()
 {
 $query = Pagamento::where('status', 'confirmado')
 ->where('caixa_id', $this->id);
 return $query->get();
 }
}