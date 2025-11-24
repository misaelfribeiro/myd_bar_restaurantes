<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class License extends Model
{
 use HasFactory;
 protected $fillable = [
 'license_key',
 'tenant_code',
 'cliente_nome',
 'cliente_email',
 'cliente_documento',
 'cliente_telefone',
 'tipo',
 'max_usuarios',
 'max_mesas',
 'modulo_delivery',
 'modulo_rh',
 'modulo_financeiro',
 'data_ativacao',
 'data_expiracao',
 'status',
 'hardware_id',
 'tentativas_ativacao',
 'ultima_verificacao',
 'observacoes'
 ];
 protected $casts = [
 'modulo_delivery' => 'boolean',
 'modulo_rh' => 'boolean',
 'modulo_financeiro' => 'boolean',
 'data_ativacao' => 'date',
 'data_expiracao' => 'date',
 'ultima_verificacao' => 'datetime',
 ];
 public static function gerarChave()
 {
 do {
 $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4) . '-' .
 substr(md5(uniqid(rand(), true)), 0, 4) . '-' .
 substr(md5(uniqid(rand(), true)), 0, 4) . '-' .
 substr(md5(uniqid(rand(), true)), 0, 4));
 } while (self::where('license_key', $key)->exists());
 return $key;
 }
 public function isAtiva()
 {
 if ($this->status !== 'ativa') {
 return false;
 }
 if (is_null($this->data_expiracao)) {
 return true;
 }
 if (Carbon::parse($this->data_expiracao)->isPast()) {
 $this->update(['status' => 'expirada']);
 return false;
 }
 return true;
 }
 public function diasRestantes()
 {
 if (is_null($this->data_expiracao)) {
 return null;
 }
 return Carbon::now()->diffInDays($this->data_expiracao, false);
 }
 public function proximaExpiracao()
 {
 $dias = $this->diasRestantes();
 return $dias !== null && $dias <= 30 && $dias > 0;
 }
 public function ativar($hardwareId)
 {
 if ($this->hardware_id && $this->hardware_id !== $hardwareId) {
 return false;
 }
 $this->update([
 'hardware_id' => $hardwareId,
 'data_ativacao' => now(),
 'status' => 'ativa',
 'tentativas_ativacao' => $this->tentativas_ativacao + 1,
 'ultima_verificacao' => now()
 ]);
 return true;
 }
 public function registrarVerificacao()
 {
 $this->update(['ultima_verificacao' => now()]);
 }
 public function temModulo($modulo)
 {
 return $this->{'modulo_' . $modulo} ?? false;
 }
 public function getTipoFormatado()
 {
 $tipos = [
 'standard' => 'Standard',
 'premium' => 'Premium',
 'enterprise' => 'Enterprise'
 ];
 return $tipos[$this->tipo] ?? $this->tipo;
 }
 public function getStatusBadge()
 {
 $badges = [
 'ativa' => '<span class="badge bg-success">Ativa</span>',
 'expirada' => '<span class="badge bg-danger">Expirada</span>',
 'suspensa' => '<span class="badge bg-warning">Suspensa</span>',
 'cancelada' => '<span class="badge bg-secondary">Cancelada</span>',
 ];
 return $badges[$this->status] ?? $this->status;
 }
}