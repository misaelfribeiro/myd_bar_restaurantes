<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEnderecoClientesTable extends Migration
{
 public function up()
 {
 Schema::create('endereco_clientes', function (Blueprint $table) {
 $table->id();
 $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
 $table->string('apelido', 50)->nullable();
 $table->string('rua');
 $table->string('numero', 20);
 $table->string('complemento')->nullable();
 $table->string('bairro', 100);
 $table->string('cidade', 100);
 $table->string('estado', 2)->default('SP');
 $table->string('cep', 10);
 $table->text('referencia')->nullable();
 $table->boolean('padrao')->default(false);
 $table->timestamps();
 });
 }
 public function down()
 {
 Schema::dropIfExists('endereco_clientes');
 }
}