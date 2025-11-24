<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePermissoesTable extends Migration
{
 public function up()
 {
 Schema::create('permissoes', function (Blueprint $table) {
 $table->id();
 $table->string('nome');
 $table->string('slug')->unique();
 $table->string('modulo');
 $table->text('descricao')->nullable();
 $table->boolean('ativo')->default(true);
 $table->timestamps();
 });
 }
 public function down()
 {
 Schema::dropIfExists('permissoes');
 }
}