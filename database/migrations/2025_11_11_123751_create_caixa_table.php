<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCaixaTable extends Migration
{
 public function up()
 {
 Schema::create('caixa', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('usuario_id');
 $table->decimal('saldo_inicial', 10, 2);
 $table->decimal('saldo_final', 10, 2)->nullable();
 $table->decimal('total_vendas', 10, 2)->default(0);
 $table->decimal('total_dinheiro', 10, 2)->default(0);
 $table->decimal('total_cartao', 10, 2)->default(0);
 $table->decimal('total_pix', 10, 2)->default(0);
 $table->decimal('total_vale', 10, 2)->default(0);
 $table->timestamp('data_abertura');
 $table->timestamp('data_fechamento')->nullable();
 $table->string('status')->default('aberto');
 $table->text('observacoes_abertura')->nullable();
 $table->text('observacoes_fechamento')->nullable();
 $table->timestamps();
 $table->foreign('usuario_id')->references('id')->on('usuarios');
 });
 }
 public function down()
 {
 Schema::dropIfExists('caixa');
 }
}