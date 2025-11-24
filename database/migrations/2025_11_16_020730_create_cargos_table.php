<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCargosTable extends Migration
{
 public function up()
 {
 Schema::create('cargos', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('empresa_id');
 $table->string('nome');
 $table->text('descricao')->nullable();
 $table->integer('nivel_hierarquico')->default(1);
 $table->decimal('salario_base', 10, 2)->nullable();
 $table->boolean('tem_comissao')->default(false);
 $table->decimal('percentual_comissao', 5, 2)->nullable();
 $table->boolean('ativo')->default(true);
 $table->timestamps();
 $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
 });
 }
 public function down()
 {
 Schema::dropIfExists('cargos');
 }
}