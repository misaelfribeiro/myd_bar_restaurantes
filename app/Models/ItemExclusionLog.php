<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class ItemExclusionLog extends Model
{
 use HasFactory;
 protected $fillable = [
 'pedido_id',
 'item_pedido_id',
 'usuario_id',
 'produto_nome',
 'preco_unitario',
 'quantidade',
 'valor_total',
 'motivo',
 'usuario_nome',
 'usuario_role',
 'item_data'
 ];
 protected $casts = [
 'item_data' => 'array',
 'preco_unitario' => 'decimal:2',
 'valor_total' => 'decimal:2',
 'created_at' => 'datetime',
 'updated_at' => 'datetime'
 ];
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
 public function usuario()
 {
 return $this->belongsTo(Usuario::class);
 }
 public function itemPedido()
 {
 return $this->belongsTo(ItemPedido::class);
 }
 public static function logExclusion($itemPedido, $motivo = null)
 {
 $usuario = auth()->user();
 return self::create([
 'pedido_id' => $itemPedido->pedido_id,
 'item_pedido_id' => $itemPedido->id,
 'usuario_id' => $usuario->id,
 'produto_nome' => $itemPedido->produto->nome ?? 'Produto não encontrado',
 'preco_unitario' => $itemPedido->preco_unitario,
 'quantidade' => $itemPedido->quantidade,
 'valor_total' => $itemPedido->preco_unitario * $itemPedido->quantidade,
 'motivo' => $motivo,
 'usuario_nome' => $usuario->nome,
 'usuario_role' => $usuario->role,
 'item_data' => [
 'produto_id' => $itemPedido->produto_id,
 'observacoes' => $itemPedido->observacoes,
 'created_at' => $itemPedido->created_at,
 'updated_at' => $itemPedido->updated_at
 ]
 ]);
 }
 public function scopePorPeriodo($query, $inicio, $fim)
 {
 return $query->whereBetween('created_at', [$inicio, $fim]);
 }
 public function scopePorUsuario($query, $usuarioId)
 {
 return $query->where('usuario_id', $usuarioId);
 }
 public function scopePorPedido($query, $pedidoId)
 {
 return $query->where('pedido_id', $pedidoId);
 }
 public function getValorTotalFormatadoAttribute()
 {
 return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
 }
 public function getPrecoUnitarioFormatadoAttribute()
 {
 return 'R$ ' . number_format($this->preco_unitario, 2, ',', '.');
 }
 public function getDataFormatadaAttribute()
 {
 return $this->created_at->format('d/m/Y H:i:s');
 }
}