<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AjustarFuncionariosTable extends Migration
{
 public function up()
 {
 Schema::table('funcionarios', function (Blueprint $table) {
 $table->decimal('salario', 10, 2)->nullable()->change();
 $table->string('tipo_comissao')->nullable()->after('salario');
 $table->decimal('percentual_comissao', 5, 2)->nullable()->after('tipo_comissao');
 $table->boolean('ativo')->default(true)->after('status');
 });
 }
 public function down()
 {
 Schema::table('funcionarios', function (Blueprint $table) {
 $table->decimal('salario', 10, 2)->nullable(false)->change();
 $table->dropColumn(['tipo_comissao', 'percentual_comissao', 'ativo']);
 });
 }
}