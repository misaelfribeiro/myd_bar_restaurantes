<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EnderecoCliente extends Model
{
 use HasFactory;
 protected $table = 'enderecos_clientes';
 protected $fillable = [
 'cliente_id',
 'apelido',
 'rua',
 'numero',
 'complemento',
 'bairro',
 'cidade',
 'estado',
 'cep',
 'ponto_referencia',
 'padrao'
 ];
 protected $casts = [
 'padrao' => 'boolean'
 ];
 public function cliente()
 {
 return $this->belongsTo(Cliente::class);
 }
 public function getEnderecoCompletoAttribute()
 {
 return "{$this->rua}, {$this->numero}" . 
 ($this->complemento ? " - {$this->complemento}" : '') . 
 " - {$this->bairro}, {$this->cidade}/{$this->estado}";
 }
 public function tornarPadrao()
 {
 self::where('cliente_id', $this->cliente_id)
 ->where('id', '!=', $this->id)
 ->update(['padrao' => false]);
 $this->update(['padrao' => true]);
 }
}