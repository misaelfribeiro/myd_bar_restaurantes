<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddEnderecoFieldsToClientesTable extends Migration
{
 public function up()
 {
 Schema::table('clientes', function (Blueprint $table) {
 $table->string('endereco_rua')->nullable()->after('email');
 $table->string('endereco_numero', 20)->nullable()->after('endereco_rua');
 $table->string('endereco_complemento')->nullable()->after('endereco_numero');
 $table->string('endereco_bairro', 100)->nullable()->after('endereco_complemento');
 $table->string('endereco_cidade', 100)->nullable()->after('endereco_bairro');
 $table->string('endereco_cep', 10)->nullable()->after('endereco_cidade');
 });
 }
 public function down()
 {
 Schema::table('clientes', function (Blueprint $table) {
 $table->dropColumn([
 'endereco_rua',
 'endereco_numero', 
 'endereco_complemento',
 'endereco_bairro',
 'endereco_cidade',
 'endereco_cep'
 ]);
 });
 }
}