<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddCodigoAndTipoPreparoToProdutosTable extends Migration
{
 public function up()
 {
 Schema::table('produtos', function (Blueprint $table) {
 $table->string('codigo')->unique()->nullable();
 $table->enum('tipo_preparo', ['pronto', 'preparo'])->default('pronto');
 });
 }
 public function down()
 {
 Schema::table('produtos', function (Blueprint $table) {
 $table->dropColumn(['codigo', 'tipo_preparo']);
 });
 }
}