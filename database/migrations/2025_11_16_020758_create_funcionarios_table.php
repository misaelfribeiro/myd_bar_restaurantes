<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateFuncionariosTable extends Migration
{
 public function up()
 {
 Schema::create('funcionarios', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('empresa_id');
 $table->unsignedBigInteger('cargo_id');
 $table->unsignedBigInteger('user_id')->nullable();
 $table->string('nome_completo');
 $table->string('cpf', 14)->unique();
 $table->string('rg', 20)->nullable();
 $table->date('data_nascimento')->nullable();
 $table->string('telefone', 20)->nullable();
 $table->string('celular', 20)->nullable();
 $table->string('email')->nullable();
 $table->string('cep', 10)->nullable();
 $table->string('endereco')->nullable();
 $table->string('numero', 10)->nullable();
 $table->string('complemento')->nullable();
 $table->string('bairro')->nullable();
 $table->string('cidade')->nullable();
 $table->string('estado', 2)->nullable();
 $table->date('data_admissao');
 $table->date('data_demissao')->nullable();
 $table->decimal('salario', 10, 2);
 $table->enum('tipo_contrato', ['clt', 'pj', 'estagio', 'temporario'])->default('clt');
 $table->string('conta_bancaria')->nullable();
 $table->string('agencia')->nullable();
 $table->string('banco')->nullable();
 $table->enum('tipo_conta', ['corrente', 'poupanca'])->nullable();
 $table->string('pis_pasep', 20)->nullable();
 $table->string('titulo_eleitor', 20)->nullable();
 $table->string('carteira_trabalho', 20)->nullable();
 $table->enum('status', ['ativo', 'afastado', 'ferias', 'demitido'])->default('ativo');
 $table->text('observacoes')->nullable();
 $table->timestamps();
 $table->softDeletes();
 $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
 $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('restrict');
 $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
 });
 }
 public function down()
 {
 Schema::dropIfExists('funcionarios');
 }
}