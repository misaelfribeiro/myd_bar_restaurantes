<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateClientesTable extends Migration
{
 public function up()
 {
 Schema::create('clientes', function (Blueprint $table) {
 $table->id();
 $table->string('nome');
 $table->string('telefone')->unique();
 $table->string('email')->nullable();
 $table->string('cpf', 14)->nullable();
 $table->date('data_nascimento')->nullable();
 $table->boolean('ativo')->default(true);
 $table->timestamp('ultimo_pedido')->nullable();
 $table->integer('total_pedidos')->default(0);
 $table->json('preferencias')->nullable();
 $table->text('observacoes')->nullable();
 $table->timestamps();
 $table->softDeletes();
 $table->index(['telefone', 'ativo']);
 $table->index('email');
 $table->index('ultimo_pedido');
 });
 }
 public function down()
 {
 Schema::dropIfExists('clientes');
 }
}