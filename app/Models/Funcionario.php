<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
class Funcionario extends Model
{
 use HasFactory, BelongsToTenant;
 protected $fillable = [
 'tenant_code',
 'cargo_id',
 'user_id',
 'nome_completo',
 'cpf',
 'rg',
 'data_nascimento',
 'telefone',
 'celular',
 'email',
 'cep',
 'endereco',
 'numero',
 'complemento',
 'bairro',
 'cidade',
 'estado',
 'data_admissao',
 'data_demissao',
 'salario',
 'tipo_contrato',
 'conta_bancaria',
 'agencia',
 'banco',
 'tipo_conta',
 'pis_pasep',
 'titulo_eleitor',
 'carteira_trabalho',
 'status',
 'tipo_comissao',
 'percentual_comissao',
 'ativo',
 'observacoes'
 ];
 protected $casts = [
 'data_nascimento' => 'date',
 'data_admissao' => 'date',
 'data_demissao' => 'date',
 'salario' => 'decimal:2',
 'percentual_comissao' => 'decimal:2',
 'ativo' => 'boolean',
 ];
 public function empresa()
 {
 return $this->belongsTo(Empresa::class);
 }
 public function cargo()
 {
 return $this->belongsTo(Cargo::class);
 }
 public function user()
 {
 return $this->belongsTo(User::class);
 }
 public function comissoes()
 {
 return $this->hasMany(Comissao::class);
 }
 public function bonus()
 {
 return $this->hasMany(Bonus::class);
 }
 public function totalComissoesPeriodo($dataInicio, $dataFim)
 {
 return $this->comissoes()
 ->whereBetween('data_referencia', [$dataInicio, $dataFim])
 ->where('status', 'pago')
 ->sum('valor');
 }
 public function totalBonusPeriodo($dataInicio, $dataFim)
 {
 return $this->bonus()
 ->whereBetween('data_referencia', [$dataInicio, $dataFim])
 ->where('status', 'pago')
 ->sum('valor');
 }
}