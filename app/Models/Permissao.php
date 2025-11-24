<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Permissao extends Model
{
 use HasFactory;
 protected $table = 'permissoes';
 protected $fillable = [
 'nome',
 'slug',
 'modulo',
 'descricao',
 'ativo'
 ];
 protected $casts = [
 'ativo' => 'boolean',
 ];
 public function cargos()
 {
 return $this->belongsToMany(Cargo::class, 'permissoes_cargo', 'permissao_id', 'cargo_id');
 }
}