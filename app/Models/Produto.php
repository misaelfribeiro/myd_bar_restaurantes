<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Produto extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'nome',
 'descricao',
 'preco',
 'categoria_id',
 'ativo',
 'codigo',
 'tipo_preparo',
 'tenant_code',
 'destaque',
 'imagem',
 ];
 protected $casts = [
 'preco' => 'decimal:2',
 'ativo' => 'boolean',
 'destaque' => 'boolean'
 ];
 public function categoria()
 {
 return $this->belongsTo(Categoria::class);
 }
 
 public function empresa()
 {
 return $this->belongsTo(Empresa::class, 'tenant_code', 'tenant_code');
 }
 
 public function itens()
 {
 return $this->hasMany(ItemPedido::class);
 }
 public function scopeAtivo($query)
 {
 return $query->where('ativo', true);
 }
 public function getPrecoFormatadoAttribute()
 {
 return 'R$ ' . number_format($this->preco, 2, ',', '.');
 }
 public function getStatusAttribute()
 {
 return $this->ativo ? 'Ativo' : 'Inativo';
 }
}