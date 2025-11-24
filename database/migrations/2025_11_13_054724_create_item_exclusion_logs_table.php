<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateItemExclusionLogsTable extends Migration
{
 public function up()
 {
 Schema::create('item_exclusion_logs', function (Blueprint $table) {
 $table->id();
 $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
 $table->foreignId('item_pedido_id')->nullable();
 $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
 $table->string('produto_nome');
 $table->decimal('preco_unitario', 8, 2);
 $table->integer('quantidade');
 $table->decimal('valor_total', 8, 2);
 $table->text('motivo')->nullable();
 $table->string('usuario_nome');
 $table->string('usuario_role');
 $table->json('item_data')->nullable();
 $table->timestamps();
 $table->index(['pedido_id', 'created_at']);
 $table->index(['usuario_id', 'created_at']);
 $table->index('created_at');
 });
 }
 public function down()
 {
 Schema::dropIfExists('item_exclusion_logs');
 }
}