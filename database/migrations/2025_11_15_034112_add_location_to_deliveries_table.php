<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddLocationToDeliveriesTable extends Migration
{
 public function up()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->decimal('entregador_latitude', 10, 8)->nullable()->after('entregador_telefone');
 $table->decimal('entregador_longitude', 11, 8)->nullable()->after('entregador_latitude');
 $table->timestamp('entregador_localizacao_atualizada_em')->nullable()->after('entregador_longitude');
 $table->decimal('destino_latitude', 10, 8)->nullable()->after('entregador_localizacao_atualizada_em');
 $table->decimal('destino_longitude', 11, 8)->nullable()->after('destino_latitude');
 });
 }
 public function down()
 {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->dropColumn([
 'entregador_latitude',
 'entregador_longitude', 
 'entregador_localizacao_atualizada_em',
 'destino_latitude',
 'destino_longitude'
 ]);
 });
 }
}