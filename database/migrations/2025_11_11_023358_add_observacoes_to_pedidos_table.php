<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddObservacoesToPedidosTable extends Migration
{
 public function up()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->text('observacoes')->nullable()->after('status');
 });
 }
 public function down()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->dropColumn('observacoes');
 });
 }
}