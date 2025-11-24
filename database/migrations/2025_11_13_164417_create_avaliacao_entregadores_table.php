<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateAvaliacaoEntregadoresTable extends Migration
{
 public function up()
 {
 Schema::create('avaliacao_entregadores', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('entregador_id');
 $table->unsignedBigInteger('pedido_id');
 $table->unsignedBigInteger('cliente_id')->nullable();
 $table->integer('nota')->comment('1 a 5 estrelas');
 $table->text('comentario')->nullable();
 $table->timestamps();
 $table->foreign('entregador_id')->references('id')->on('entregadores')->onDelete('cascade');
 $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
 $table->foreign('cliente_id')->references('id')->on('usuarios')->onDelete('set null');
 $table->unique(['pedido_id'], 'unique_avaliacao_pedido');
 $table->index(['entregador_id', 'nota']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('avaliacao_entregadores');
 }
}