<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ItemPedido extends Model
{
 use HasFactory;
 protected $fillable = [
 'pedido_id',
 'produto_id',
 'combo_id',
 'tipo_item',
 'quantidade',
 'preco_unitario',
 'subtotal',
 'observacoes'
 ];

 protected $casts = [
 'tipo_item' => 'string'
 ];
 public function pedido()
 {
 return $this->belongsTo(Pedido::class);
 }
 public function produto()
 {
 return $this->belongsTo(Produto::class);
 }

 public function combo()
 {
 return $this->belongsTo(Combo::class);
 }

 public function estornos()
 {
 return $this->hasMany(Estorno::class, 'item_pedido_id');
 }

 // Método helper para pegar o item (produto ou combo)
 public function getItemAttribute()
 {
 return $this->tipo_item === 'combo' ? $this->combo : $this->produto;
 }

 // Método helper para pegar o nome do item
 public function getNomeItemAttribute()
 {
 if ($this->tipo_item === 'combo') {
 return $this->combo ? $this->combo->nome : 'Combo não encontrado';
 }
 return $this->produto ? $this->produto->nome : 'Produto não encontrado';
 }
}