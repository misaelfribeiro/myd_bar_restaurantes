<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePagamentosTable extends Migration
{
 public function up()
 {
 Schema::create('pagamentos', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('pedido_id');
 $table->string('forma_pagamento');
 $table->decimal('valor', 10, 2);
 $table->decimal('valor_recebido', 10, 2)->nullable();
 $table->decimal('troco', 10, 2)->nullable();
 $table->string('status')->default('pendente');
 $table->text('observacoes')->nullable();
 $table->unsignedBigInteger('usuario_id');
 $table->timestamp('data_pagamento')->nullable();
 $table->timestamps();
 $table->foreign('pedido_id')->references('id')->on('pedidos');
 $table->foreign('usuario_id')->references('id')->on('usuarios');
 });
 }
 public function down()
 {
 Schema::dropIfExists('pagamentos');
 }
}