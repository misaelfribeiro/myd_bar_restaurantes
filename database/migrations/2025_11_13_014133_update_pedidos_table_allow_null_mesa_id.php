<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class UpdatePedidosTableAllowNullMesaId extends Migration
{
 public function up()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->unsignedBigInteger('mesa_id')->nullable()->change();
 });
 }
 public function down()
 {
 Schema::table('pedidos', function (Blueprint $table) {
 $table->unsignedBigInteger('mesa_id')->nullable(false)->change();
 });
 }
}