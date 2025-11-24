<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateComissoesTable extends Migration
{
 public function up()
 {
 Schema::create('comissoes', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('funcionario_id');
 $table->unsignedBigInteger('pedido_id')->nullable();
 $table->string('tipo');
 $table->string('descricao');
 $table->decimal('valor_base', 10, 2);
 $table->decimal('percentual', 5, 2);
 $table->decimal('valor_comissao', 10, 2);
 $table->date('data_referencia');
 $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
 $table->date('data_pagamento')->nullable();
 $table->timestamps();
 $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('cascade');
 });
 }
 public function down()
 {
 Schema::dropIfExists('comissoes');
 }
}