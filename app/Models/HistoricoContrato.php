<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HistoricoContrato extends Model
{
 protected $table = 'historico_contratos';
 protected $fillable = [
 'contrato_id',
 'empresa_id',
 'usuario_id',
 'acao',
 'descricao',
 'dados_anteriores',
 'dados_novos',
 'ip_address',
 ];
 protected $casts = [
 'dados_anteriores' => 'array',
 'dados_novos' => 'array',
 ];
 public function contrato()
 {
 return $this->belongsTo(Contrato::class);
 }
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public function usuario()
 {
 return $this->belongsTo(User::class);
 }
 public static function registrar($contratoId, $acao, $descricao, $dadosAnteriores = null, $dadosNovos = null)
 {
 $contrato = Contrato::find($contratoId);
 return static::create([
 'contrato_id' => $contratoId,
 'empresa_id' => $contrato->empresa_id,
 'usuario_id' => auth()->id(),
 'acao' => $acao,
 'descricao' => $descricao,
 'dados_anteriores' => $dadosAnteriores,
 'dados_novos' => $dadosNovos,
 'ip_address' => request()->ip(),
 ]);
 }
}