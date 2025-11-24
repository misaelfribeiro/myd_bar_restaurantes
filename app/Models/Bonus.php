<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Bonus extends Model
{
 use HasFactory, BelongsToTenant;
 protected $table = 'bonus';
 protected $fillable = [
 'funcionario_id',
 'tenant_code',
 'tipo',
 'titulo',
 'descricao',
 'data_referencia',
 'valor',
 'status',
 'data_pagamento',
 'observacoes'
 ];
 protected $casts = [
 'data_referencia' => 'date',
 'data_pagamento' => 'date',
 'valor' => 'decimal:2',
 ];
 public function funcionario()
 {
 return $this->belongsTo(Funcionario::class);
 }
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
}