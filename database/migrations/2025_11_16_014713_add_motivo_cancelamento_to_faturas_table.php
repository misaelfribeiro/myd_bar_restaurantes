<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddMotivoCancelamentoToFaturasTable extends Migration
{
 public function up()
 {
 Schema::table('faturas', function (Blueprint $table) {
 $table->text('motivo_cancelamento')->nullable()->after('observacoes');
 });
 }
 public function down()
 {
 Schema::table('faturas', function (Blueprint $table) {
 $table->dropColumn('motivo_cancelamento');
 });
 }
}