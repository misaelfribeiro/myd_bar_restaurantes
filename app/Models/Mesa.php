<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Mesa extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'identificador',
 'lugares',
 'numero',
 'capacidade',
 'status',
 'tenant_code',
 ];
 public function pedidos()
 {
 return $this->hasMany(Pedido::class);
 }
}