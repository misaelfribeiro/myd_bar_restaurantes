<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Cargo extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'tenant_code',
 'nome',
 'descricao',
 'nivel_hierarquico',
 'salario_base',
 'ativo'
 ];
 protected $casts = [
 'salario_base' => 'decimal:2',
 'ativo' => 'boolean',
 ];
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public function funcionarios()
 {
 return $this->hasMany(Funcionario::class);
 }
 public function permissoes()
 {
 return $this->belongsToMany(Permissao::class, 'permissoes_cargo', 'cargo_id', 'permissao_id');
 }
 public function temPermissao($slug)
 {
 return $this->permissoes()->where('slug', $slug)->exists();
 }
}