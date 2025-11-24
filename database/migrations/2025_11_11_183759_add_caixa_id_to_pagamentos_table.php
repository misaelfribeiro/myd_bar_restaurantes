<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddCaixaIdToPagamentosTable extends Migration
{
 public function up()
 {
 Schema::table('pagamentos', function (Blueprint $table) {
 $table->unsignedBigInteger('caixa_id')->nullable()->after('pedido_id');
 $table->foreign('caixa_id')->references('id')->on('caixa');
 });
 }
 public function down()
 {
 Schema::table('pagamentos', function (Blueprint $table) {
 $table->dropForeign(['caixa_id']);
 $table->dropColumn('caixa_id');
 });
 }
}