<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Plano extends Model
{
 protected $fillable = [
 'codigo',
 'nome',
 'descricao',
 'valor_mensal',
 'valor_anual',
 'desconto_anual_percent',
 'max_usuarios',
 'max_produtos',
 'max_pedidos_mes',
 'max_filiais',
 'max_mesas',
 'max_entregadores',
 'taxa_transacao_percent',
 'taxa_fixa_pedido',
 'taxa_delivery_percent',
 'recursos',
 'dias_trial',
 'permite_trial',
 'ativo',
 'ordem',
 'destaque',
 ];
 protected $casts = [
 'valor_mensal' => 'decimal:2',
 'valor_anual' => 'decimal:2',
 'desconto_anual_percent' => 'decimal:2',
 'taxa_transacao_percent' => 'decimal:2',
 'taxa_fixa_pedido' => 'decimal:2',
 'taxa_delivery_percent' => 'decimal:2',
 'recursos' => 'array',
 'ativo' => 'boolean',
 'permite_trial' => 'boolean',
 'destaque' => 'boolean',
 ];
 public function contratos()
 {
 return $this->hasMany(Contrato::class);
 }
 public function empresasAtivas()
 {
 return $this->hasManyThrough(
 Empresa::class,
 Contrato::class,
 'plano_id',
 'id',
 'id',
 'empresa_id'
 )->where('contratos.status', 'ativo');
 }
 public function getEconomiaAnualAttribute()
 {
 $valorMensalAnual = $this->valor_mensal * 12;
 return $valorMensalAnual - $this->valor_anual;
 }
 public function temRecurso($recurso)
 {
 return in_array($recurso, $this->recursos ?? []);
 }
 public function scopeAtivo($query)
 {
 return $query->where('ativo', true);
 }
 public function scopeOrdenado($query)
 {
 return $query->orderBy('ordem')->orderBy('valor_mensal');
 }
}