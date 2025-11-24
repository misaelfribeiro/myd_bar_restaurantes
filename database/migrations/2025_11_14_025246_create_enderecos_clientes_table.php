<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEnderecosClientesTable extends Migration
{
 public function up()
 {
 Schema::create('enderecos_clientes', function (Blueprint $table) {
 $table->id();
 $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
 $table->string('apelido')->default('Principal');
 $table->string('rua');
 $table->string('numero');
 $table->string('complemento')->nullable();
 $table->string('bairro');
 $table->string('cidade');
 $table->string('estado', 2)->default('SP');
 $table->string('cep', 9);
 $table->text('ponto_referencia')->nullable();
 $table->boolean('padrao')->default(false);
 $table->timestamps();
 $table->index(['cliente_id', 'padrao']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('enderecos_clientes');
 }
}