<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;
class Delivery extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'cliente_id',
 'cliente_nome',
 'cliente_telefone',
 'cliente_email',
 'endereco_rua',
 'endereco_numero',
 'endereco_complemento',
 'endereco_bairro',
 'endereco_cidade',
 'endereco_cep',
 'endereco_referencia',
 'taxa_entrega',
 'tempo_estimado',
 'distancia_km',
 'status',
 'pedido_id',
 'entregador_id',
 'tenant_code',
 'tipo_entrega',
 'disponivel_plataforma',
 'disponibilizado_em',
 'aceito_em',
 'valor_entregador',
 'entregador_nome',
 'entregador_telefone',
 'entregador_latitude',
 'entregador_longitude',
 'entregador_localizacao_atualizada_em',
 'destino_latitude',
 'destino_longitude',
 'data_pedido',
 'data_confirmacao',
 'data_saida',
 'data_entrega',
 'observacoes',
 'observacoes_internas',
 'avaliacao',
 'comentario_avaliacao',
 'tentativas_notificacao',
 'ultima_notificacao_em',
 'entregadores_notificados',
 'raio_busca_km'
 ];
 protected $casts = [
 'taxa_entrega' => 'decimal:2',
 'distancia_km' => 'decimal:2',
 'tempo_estimado' => 'integer',
 'valor_entregador' => 'decimal:2',
 'disponivel_plataforma' => 'boolean',
 'entregador_latitude' => 'decimal:8',
 'entregador_longitude' => 'decimal:8',
 'destino_latitude' => 'decimal:8',
 'destino_longitude' => 'decimal:8',
 'data_pedido' => 'datetime',
 'data_confirmacao' => 'datetime',
 'data_saida' => 'datetime',
 'data_entrega' => 'datetime',
 'disponibilizado_em' => 'datetime',
 'aceito_em' => 'datetime',
 'entregador_localizacao_atualizada_em' => 'datetime',
 'ultima_notificacao_em' => 'datetime',
 'entregadores_notificados' => 'array',
 'avaliacao' => 'integer'
 ];
 protected $attributes = [
 'status' => 'pendente'
 ];
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
 public function cliente()
 {
 return $this->belongsTo(Cliente::class);
 }
 public function entregador()
 {
 return $this->belongsTo(Entregador::class);
 }
 public function getEnderecoCompletoAttribute()
 {
 $endereco = "{$this->endereco_rua}, {$this->endereco_numero}";
 if ($this->endereco_complemento) {
 $endereco .= ", {$this->endereco_complemento}";
 }
 $endereco .= " - {$this->endereco_bairro}, {$this->endereco_cidade} - {$this->endereco_cep}";
 return $endereco;
 }
 public function getStatusLabelAttribute()
 {
 $labels = [
 'pendente' => 'Pendente',
 'confirmado' => 'Confirmado',
 'preparando' => 'Preparando',
 'pronto' => 'Pronto',
 'saiu_entrega' => 'Saiu para Entrega',
 'entregue' => 'Entregue',
 'cancelado' => 'Cancelado'
 ];
 return $labels[$this->status] ?? $this->status;
 }
 public function getStatusColorAttribute()
 {
 $colors = [
 'pendente' => 'warning',
 'confirmado' => 'info',
 'preparando' => 'primary',
 'pronto' => 'success',
 'saiu_entrega' => 'dark',
 'entregue' => 'success',
 'cancelado' => 'danger'
 ];
 return $colors[$this->status] ?? 'secondary';
 }
 public function getTempoDecorridoAttribute()
 {
 if (!$this->data_pedido) {
 return null;
 }
 $inicio = Carbon::parse($this->data_pedido);
 $fim = $this->data_entrega ? Carbon::parse($this->data_entrega) : Carbon::now();
 return $inicio->diff($fim);
 }
 public function getTempoRestanteAttribute()
 {
 if ($this->status === 'entregue' || $this->status === 'cancelado') {
 return null;
 }
 $inicio = Carbon::parse($this->data_pedido);
 $tempoLimite = $inicio->addMinutes($this->tempo_estimado);
 if (Carbon::now()->gt($tempoLimite)) {
 return 'Atrasado';
 }
 $restante = Carbon::now()->diff($tempoLimite);
 if ($restante->h > 0) {
 return "{$restante->h}h {$restante->i}min";
 }
 return "{$restante->i}min";
 }
 public function setClienteTelefoneAttribute($value)
 {
 $this->attributes['cliente_telefone'] = preg_replace('/[^0-9]/', '', $value);
 }
 public function setEnderecoCepAttribute($value)
 {
 $this->attributes['endereco_cep'] = preg_replace('/[^0-9]/', '', $value);
 }
 public function scopePendentes($query)
 {
 return $query->where('status', 'pendente');
 }
 public function scopeEmAndamento($query)
 {
 return $query->whereIn('status', ['confirmado', 'preparando', 'pronto', 'saiu_entrega']);
 }
 public function scopeEntregues($query)
 {
 return $query->where('status', 'entregue');
 }
 public function scopeCancelados($query)
 {
 return $query->where('status', 'cancelado');
 }
 public function scopeAtrasados($query)
 {
 return $query->whereNotIn('status', ['entregue', 'cancelado'])
 ->where('data_pedido', '<', Carbon::now()->subMinutes(60));
 }
 public function scopePorBairro($query, $bairro)
 {
 return $query->where('endereco_bairro', 'like', "%{$bairro}%");
 }
 public function confirmar()
 {
 $this->update([
 'status' => 'confirmado',
 'data_confirmacao' => Carbon::now()
 ]);
 }
 public function iniciarPreparo()
 {
 $this->update(['status' => 'preparando']);
 }
 public function marcarPronto()
 {
 $this->update(['status' => 'pronto']);
 }
 public function sairParaEntrega($entregador = null)
 {
 $data = [
 'status' => 'saiu_entrega',
 'data_saida' => Carbon::now()
 ];
 if ($entregador) {
 $data['entregador_nome'] = $entregador;
 }
 $this->update($data);
 }
 public function marcarEntregue($avaliacao = null, $comentario = null)
 {
 $data = [
 'status' => 'entregue',
 'data_entrega' => Carbon::now()
 ];
 if ($avaliacao) {
 $data['avaliacao'] = $avaliacao;
 }
 if ($comentario) {
 $data['comentario_avaliacao'] = $comentario;
 }
 $this->update($data);
 }
 public function cancelar($motivo = null)
 {
 $data = ['status' => 'cancelado'];
 if ($motivo) {
 $data['observacoes_internas'] = $motivo;
 }
 $this->update($data);
 }
 public function calcularTaxaEntrega()
 {
 $taxaBase = 5.00;
 if ($this->distancia_km) {
 if ($this->distancia_km > 5) {
 $taxaBase += ($this->distancia_km - 5) * 2;
 }
 }
 return $taxaBase;
 }
 public function estimarTempo()
 {
 $tempoBase = 30;
 if ($this->distancia_km) {
 $tempoBase += ($this->distancia_km * 3);
 }
 return $tempoBase;
 }
 
 // Métodos para gestão de entrega por plataforma
 public function disponibilizarParaPlataforma($valorEntregador = null)
 {
 $this->update([
 'tipo_entrega' => 'plataforma',
 'disponivel_plataforma' => true,
 'disponibilizado_em' => Carbon::now(),
 'valor_entregador' => $valorEntregador ?? ($this->taxa_entrega * 0.7) // 70% da taxa para o entregador
 ]);
 }
 
 public function atribuirEntregadorFixo($entregadorId)
 {
 $entregador = Entregador::find($entregadorId);
 if ($entregador) {
 $this->update([
 'tipo_entrega' => 'fixo',
 'entregador_id' => $entregador->id,
 'entregador_nome' => $entregador->nome,
 'entregador_telefone' => $entregador->telefone,
 'disponivel_plataforma' => false
 ]);
 }
 }
 
 public function aceitarPorEntregador($entregadorId)
 {
 $entregador = Entregador::find($entregadorId);
 if ($entregador && $this->disponivel_plataforma) {
 $this->update([
 'entregador_id' => $entregador->id,
 'entregador_nome' => $entregador->nome,
 'entregador_telefone' => $entregador->telefone,
 'disponivel_plataforma' => false,
 'aceito_em' => Carbon::now(),
 'status' => 'confirmado'
 ]);
 return true;
 }
 return false;
 }
 
 // Scopes
 public function scopeDisponiveis($query)
 {
 return $query->where('disponivel_plataforma', true)
 ->where('status', 'pendente');
 }
 
 public function scopeComEntregadorFixo($query)
 {
 return $query->where('tipo_entrega', 'fixo');
 }
 
 public function scopeDaPlataforma($query)
 {
 return $query->where('tipo_entrega', 'plataforma');
 }
 
 /**
 * Busca entregadores próximos disponíveis para notificar
 */
 public function buscarEntregadoresProximos()
 {
 $entregadoresJaNotificados = $this->entregadores_notificados ?? [];
 
 $entregadores = Entregador::where('status', 'ativo')
 ->where('disponivel', 1)
 ->whereNotIn('id', $entregadoresJaNotificados)
 ->get();
 
 // Filtra por distância se houver coordenadas
 if ($this->destino_latitude && $this->destino_longitude) {
 $entregadores = $entregadores->filter(function($entregador) {
 if (!$entregador->latitude || !$entregador->longitude) {
 return false;
 }
 
 $distancia = $this->calcularDistancia(
 $this->destino_latitude,
 $this->destino_longitude,
 $entregador->latitude,
 $entregador->longitude
 );
 
 return $distancia <= $this->raio_busca_km;
 });
 }
 
 return $entregadores;
 }
 
 /**
 * Notifica entregadores disponíveis sobre nova entrega
 */
 public function notificarEntregadores()
 {
 $entregadores = $this->buscarEntregadoresProximos();
 
 if ($entregadores->isEmpty()) {
 // Aumenta o raio de busca se não encontrou ninguém
 $this->increment('raio_busca_km', 5);
 return ['success' => false, 'message' => 'Nenhum entregador disponível no raio de ' . $this->raio_busca_km . 'km'];
 }
 
 $notificados = $this->entregadores_notificados ?? [];
 
 foreach ($entregadores as $entregador) {
 // Aqui você pode implementar notificação push, SMS, etc
 // Por enquanto, apenas registra que foi notificado
 $notificados[] = $entregador->id;
 }
 
 $this->update([
 'entregadores_notificados' => $notificados,
 'ultima_notificacao_em' => now(),
 'tentativas_notificacao' => $this->tentativas_notificacao + 1
 ]);
 
 return [
 'success' => true,
 'message' => count($entregadores) . ' entregadores notificados',
 'entregadores' => $entregadores->pluck('nome')
 ];
 }
 
 /**
 * Calcula distância entre dois pontos (Haversine)
 */
 private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
 {
 $raioTerra = 6371; // em km
 
 $dLat = deg2rad($lat2 - $lat1);
 $dLon = deg2rad($lon2 - $lon1);
 
 $a = sin($dLat/2) * sin($dLat/2) +
 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
 sin($dLon/2) * sin($dLon/2);
 
 $c = 2 * atan2(sqrt($a), sqrt(1-$a));
 $distancia = $raioTerra * $c;
 
 return $distancia;
 }
}