<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateBonusTable extends Migration
{
 public function up()
 {
 Schema::create('bonus', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('funcionario_id');
 $table->string('tipo');
 $table->string('titulo');
 $table->text('descricao')->nullable();
 $table->decimal('valor', 10, 2);
 $table->date('data_referencia');
 $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
 $table->date('data_pagamento')->nullable();
 $table->unsignedBigInteger('aprovado_por')->nullable();
 $table->timestamp('aprovado_em')->nullable();
 $table->timestamps();
 $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('cascade');
 });
 }
 public function down()
 {
 Schema::dropIfExists('bonus');
 }
}