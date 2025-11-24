<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AvaliacaoEntregador extends Model
{
 use HasFactory;
 protected $table = 'avaliacao_entregadores';
 protected $fillable = [
 'entregador_id',
 'pedido_id',
 'cliente_id',
 'nota',
 'comentario'
 ];
 protected $casts = [
 'nota' => 'integer'
 ];
 public function entregador()
 {
 return $this->belongsTo(Entregador::class);
 }
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
 public function cliente()
 {
 return $this->belongsTo(Usuario::class, 'cliente_id');
 }
 public function scopePorNota($query, $nota)
 {
 return $query->where('nota', $nota);
 }
 public function scopePositivas($query)
 {
 return $query->where('nota', '>=', 4);
 }
 public function scopeNegativas($query)
 {
 return $query->where('nota', '<=', 2);
 }
 public function getNotaTextoAttribute()
 {
 $notas = [
 1 => 'Muito Ruim',
 2 => 'Ruim',
 3 => 'Regular',
 4 => 'Bom',
 5 => 'Excelente'
 ];
 return $notas[$this->nota] ?? 'Não avaliado';
 }
 public function getEstrelasAttribute()
 {
 return str_repeat('⭐', $this->nota) . str_repeat('☆', 5 - $this->nota);
 }
 public static function boot()
 {
 parent::boot();
 static::created(function ($avaliacao) {
 $avaliacao->entregador->atualizarEstatisticas();
 });
 static::updated(function ($avaliacao) {
 $avaliacao->entregador->atualizarEstatisticas();
 });
 static::deleted(function ($avaliacao) {
 $avaliacao->entregador->atualizarEstatisticas();
 });
 }
}