<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddEntregadorIdToDeliveriesTable extends Migration
{
 public function up()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->foreignId('entregador_id')->nullable()->after('pedido_id')->constrained('entregadores')->onDelete('set null');
 });
 }
 public function down()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->dropForeign(['entregador_id']);
 $table->dropColumn('entregador_id');
 });
 }
}