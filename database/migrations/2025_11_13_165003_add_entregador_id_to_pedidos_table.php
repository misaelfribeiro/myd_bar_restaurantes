<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddEntregadorIdToPedidosTable extends Migration
{
 public function up()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->unsignedBigInteger('entregador_id')->nullable()->after('total');
 $table->timestamp('saiu_entrega')->nullable()->after('entregador_id');
 $table->timestamp('entregue_em')->nullable()->after('saiu_entrega');
 $table->decimal('taxa_entrega', 8, 2)->default(0)->after('entregue_em');
 $table->text('observacoes_entrega')->nullable()->after('taxa_entrega');
 $table->foreign('entregador_id')->references('id')->on('entregadores')->onDelete('set null');
 $table->index(['entregador_id', 'status']);
 });
 }
 public function down()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->dropForeign(['entregador_id']);
 $table->dropIndex(['entregador_id', 'status']);
 $table->dropColumn([
 'entregador_id',
 'saiu_entrega',
 'entregue_em',
 'taxa_entrega',
 'observacoes_entrega'
 ]);
 });
 }
}