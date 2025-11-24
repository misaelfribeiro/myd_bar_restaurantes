<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Empresa extends Model
{
 use HasFactory, SoftDeletes;
 protected $fillable = [
 'nome_fantasia',
 'razao_social',
 'cnpj',
 'inscricao_estadual',
 'inscricao_municipal',
 'telefone',
 'celular',
 'email',
 'site',
 'endereco_rua',
 'endereco_numero',
 'endereco_complemento',
 'endereco_bairro',
 'endereco_cidade',
 'endereco_estado',
 'endereco_cep',
 'latitude',
 'longitude',
 'tipo',
 'empresa_matriz_id',
 'logo',
 'descricao',
 'horario_abertura',
 'horario_fechamento',
 'dias_funcionamento',
 'aceita_delivery',
 'taxa_entrega_padrao',
 'raio_entrega_km',
 'tempo_entrega_minutos',
 'pedido_minimo',
 'ativo',
 'is_master',
 'tenant_code',
 'plano',
 'data_inicio_contrato',
 'data_fim_contrato',
 'status_contrato',
 'max_usuarios',
 'max_produtos',
 'max_pedidos_mes',
 'max_filiais',
 'valor_mensalidade',
 'taxa_transacao_percent',
 'taxa_fixa_pedido',
 'dominio_personalizado',
 'cor_primaria',
 'cor_secundaria',
 'permite_white_label',
 'recursos_habilitados'
 ];
 protected $casts = [
 'latitude' => 'decimal:8',
 'longitude' => 'decimal:8',
 'taxa_entrega_padrao' => 'decimal:2',
 'raio_entrega_km' => 'decimal:2',
 'pedido_minimo' => 'decimal:2',
 'aceita_delivery' => 'boolean',
 'ativo' => 'boolean',
 'dias_funcionamento' => 'array',
 'is_master' => 'boolean',
 'data_inicio_contrato' => 'date',
 'data_fim_contrato' => 'date',
 'valor_mensalidade' => 'decimal:2',
 'taxa_transacao_percent' => 'decimal:2',
 'taxa_fixa_pedido' => 'decimal:2',
 'permite_white_label' => 'boolean',
 'recursos_habilitados' => 'array'
 ];
 public function matriz()
 {
 return $this->belongsTo(Empresa::class, 'empresa_matriz_id');
 }
 public function filiais()
 {
 return $this->hasMany(Empresa::class, 'empresa_matriz_id');
 }
 public function getEnderecoCompletoAttribute()
 {
 $endereco = "{$this->endereco_rua}, {$this->endereco_numero}";
 if ($this->endereco_complemento) {
 $endereco .= ", {$this->endereco_complemento}";
 }
 $endereco .= " - {$this->endereco_bairro}, {$this->endereco_cidade}/{$this->endereco_estado} - {$this->endereco_cep}";
 return $endereco;
 }
 public function getTipoLabelAttribute()
 {
 return $this->tipo === 'matriz' ? 'Matriz' : 'Filial';
 }
 public function getStatusBadgeAttribute()
 {
 if ($this->ativo) {
 return '<span class="badge bg-success">Ativo</span>';
 }
 return '<span class="badge bg-danger">Inativo</span>';
 }
 public function getPlanoLabelAttribute()
 {
 $planos = [
 'basico' => 'Básico',
 'profissional' => 'Profissional',
 'premium' => 'Premium',
 'enterprise' => 'Enterprise'
 ];
 return $planos[$this->plano] ?? $this->plano;
 }
 public function getStatusContratoBadgeAttribute()
 {
 $badges = [
 'ativo' => '<span class="badge bg-success">Ativo</span>',
 'trial' => '<span class="badge bg-info">Trial</span>',
 'suspenso' => '<span class="badge bg-warning">Suspenso</span>',
 'cancelado' => '<span class="badge bg-danger">Cancelado</span>'
 ];
 return $badges[$this->status_contrato] ?? '<span class="badge bg-secondary">Desconhecido</span>';
 }
 public function getDiasRestantesContratoAttribute()
 {
 if (!$this->data_fim_contrato) {
 return null;
 }
 return now()->diffInDays($this->data_fim_contrato, false);
 }
 public function setCnpjAttribute($value)
 {
 $this->attributes['cnpj'] = preg_replace('/[^0-9]/', '', $value);
 }
 public function setTelefoneAttribute($value)
 {
 $this->attributes['telefone'] = preg_replace('/[^0-9]/', '', $value);
 }
 public function setCelularAttribute($value)
 {
 $this->attributes['celular'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
 }
 public function setEnderecoCepAttribute($value)
 {
 $this->attributes['endereco_cep'] = preg_replace('/[^0-9]/', '', $value);
 }
 public function scopeAtivas($query)
 {
 return $query->where('ativo', true);
 }
 public function scopeMatrizes($query)
 {
 return $query->where('tipo', 'matriz');
 }
 public function scopeFiliais($query)
 {
 return $query->where('tipo', 'filial');
 }
 public function scopeMaster($query)
 {
 return $query->where('is_master', true);
 }
 public function scopeTenants($query)
 {
 return $query->where('is_master', false);
 }
 public function scopeContratoAtivo($query)
 {
 return $query->where('status_contrato', 'ativo')
 ->where(function($q) {
 $q->whereNull('data_fim_contrato')
 ->orWhere('data_fim_contrato', '>=', now());
 });
 }
 public function podeAdicionarUsuario()
 {
 $totalUsuarios = \App\Models\Usuario::where('empresa_id', $this->id)->count();
 return $totalUsuarios < $this->max_usuarios;
 }
 public function podeAdicionarProduto()
 {
 $totalProdutos = \App\Models\Produto::where('empresa_id', $this->id)->count();
 return $totalProdutos < $this->max_produtos;
 }
 public function podeAdicionarFilial()
 {
 if ($this->tipo !== 'matriz') {
 return false;
 }
 $totalFiliais = $this->filiais()->count();
 return $totalFiliais < $this->max_filiais;
 }
 public function atingiuLimitePedidosMes()
 {
 $pedidosNoMes = \App\Models\Pedido::where('empresa_id', $this->id)
 ->whereMonth('created_at', now()->month)
 ->whereYear('created_at', now()->year)
 ->count();
 return $pedidosNoMes >= $this->max_pedidos_mes;
 }
 public function possuiRecurso($recurso)
 {
 if ($this->is_master) {
 return true;
 }
 $recursos = $this->recursos_habilitados ?? [];
 return in_array($recurso, $recursos);
 }
 public function calcularTaxaPedido($valorPedido)
 {
 $taxaPercentual = ($valorPedido * $this->taxa_transacao_percent) / 100;
 return $taxaPercentual + $this->taxa_fixa_pedido;
 }
 public function gerarTenantCode()
 {
 if ($this->tenant_code) {
 return $this->tenant_code;
 }
 $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->nome_fantasia), 0, 8));
 $numero = str_pad(Empresa::max('id') + 1, 4, '0', STR_PAD_LEFT);
 return $base . $numero;
 }
 protected static function boot()
 {
 parent::boot();
 static::creating(function ($empresa) {
 if (!$empresa->tenant_code && !$empresa->is_master) {
 $empresa->tenant_code = $empresa->gerarTenantCode();
 }
 });
 }
}