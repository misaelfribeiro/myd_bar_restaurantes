<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Categoria extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'nome',
 'tenant_code',
 ];
 public function produtos()
 {
 return $this->hasMany(Produto::class);
 }
}