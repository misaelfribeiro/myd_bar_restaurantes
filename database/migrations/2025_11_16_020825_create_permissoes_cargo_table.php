<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePermissoesCargoTable extends Migration
{
 public function up()
 {
 Schema::create('permissoes_cargo', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('cargo_id');
 $table->unsignedBigInteger('permissao_id');
 $table->timestamps();
 $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('cascade');
 $table->foreign('permissao_id')->references('id')->on('permissoes')->onDelete('cascade');
 $table->unique(['cargo_id', 'permissao_id']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('permissoes_cargo');
 }
}