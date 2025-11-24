<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEmpresasTable extends Migration
{
 public function up()
 {
 Schema::create('empresas', function (Blueprint $table) {
 $table->id();
 $table->string('nome_fantasia');
 $table->string('razao_social');
 $table->string('cnpj', 18)->unique();
 $table->string('inscricao_estadual')->nullable();
 $table->string('inscricao_municipal')->nullable();
 $table->string('telefone');
 $table->string('celular')->nullable();
 $table->string('email')->unique();
 $table->string('site')->nullable();
 $table->string('endereco_rua');
 $table->string('endereco_numero');
 $table->string('endereco_complemento')->nullable();
 $table->string('endereco_bairro');
 $table->string('endereco_cidade');
 $table->string('endereco_estado', 2);
 $table->string('endereco_cep', 9);
 $table->decimal('latitude', 10, 8)->nullable();
 $table->decimal('longitude', 11, 8)->nullable();
 $table->enum('tipo', ['matriz', 'filial'])->default('matriz');
 $table->foreignId('empresa_matriz_id')->nullable()->constrained('empresas')->onDelete('cascade');
 $table->string('logo')->nullable();
 $table->text('descricao')->nullable();
 $table->time('horario_abertura')->nullable();
 $table->time('horario_fechamento')->nullable();
 $table->json('dias_funcionamento')->nullable();
 $table->boolean('aceita_delivery')->default(true);
 $table->decimal('taxa_entrega_padrao', 10, 2)->default(0);
 $table->decimal('raio_entrega_km', 10, 2)->nullable();
 $table->decimal('pedido_minimo', 10, 2)->default(0);
 $table->boolean('ativo')->default(true);
 $table->timestamps();
 $table->softDeletes();
 $table->index('cnpj');
 $table->index('tipo');
 $table->index('empresa_matriz_id');
 $table->index(['latitude', 'longitude']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('empresas');
 }
}