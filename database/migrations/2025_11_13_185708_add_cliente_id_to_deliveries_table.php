<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddClienteIdToDeliveriesTable extends Migration
{
 public function up()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->unsignedBigInteger('cliente_id')->nullable()->after('id');
 $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null');
 $table->string('cliente_nome')->nullable()->change();
 $table->string('cliente_telefone')->nullable()->change();
 $table->string('cliente_email')->nullable()->change();
 });
 }
 public function down()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->dropForeign(['cliente_id']);
 $table->dropColumn('cliente_id');
 $table->string('cliente_nome')->nullable(false)->change();
 $table->string('cliente_telefone')->nullable(false)->change();
 });
 }
}