<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateDeliveriesTable extends Migration
{
 public function up()
 {
 Schema::create('deliveries', function (Blueprint $table) {
 $table->id();
 $table->string('cliente_nome');
 $table->string('cliente_telefone');
 $table->string('cliente_email')->nullable();
 $table->string('endereco_rua');
 $table->string('endereco_numero');
 $table->string('endereco_complemento')->nullable();
 $table->string('endereco_bairro');
 $table->string('endereco_cidade');
 $table->string('endereco_cep', 9);
 $table->text('endereco_referencia')->nullable();
 $table->decimal('taxa_entrega', 8, 2)->default(0);
 $table->integer('tempo_estimado')->default(30);
 $table->decimal('distancia_km', 5, 2)->nullable();
 $table->enum('status', [
 'pendente',
 'confirmado',
 'preparando',
 'pronto',
 'saiu_entrega',
 'entregue',
 'cancelado'
 ])->default('pendente');
 $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('cascade');
 $table->foreignId('entregador_id')->nullable()->constrained('entregadores')->onDelete('set null');
 $table->string('entregador_nome')->nullable();
 $table->string('entregador_telefone')->nullable();
 $table->timestamp('data_pedido')->useCurrent();
 $table->timestamp('data_confirmacao')->nullable();
 $table->timestamp('data_saida')->nullable();
 $table->timestamp('data_entrega')->nullable();
 $table->text('observacoes')->nullable();
 $table->text('observacoes_internas')->nullable();
 $table->integer('avaliacao')->nullable()->comment('1-5 estrelas');
 $table->text('comentario_avaliacao')->nullable();
 $table->timestamps();
 $table->index(['status', 'created_at']);
 $table->index(['cliente_telefone']);
 $table->index(['endereco_bairro']);
 $table->index(['data_pedido']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('deliveries');
 }
}