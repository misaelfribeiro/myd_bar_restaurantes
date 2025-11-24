<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEntregadoresTable extends Migration
{
 public function up()
 {
 Schema::create('entregadores', function (Blueprint $table) {
 $table->id();
 $table->string('nome');
 $table->string('email')->unique();
 $table->string('telefone');
 $table->string('whatsapp')->nullable();
 $table->string('cpf', 14)->unique();
 $table->string('rg')->nullable();
 $table->date('data_nascimento');
 $table->string('cep', 10);
 $table->string('endereco');
 $table->string('numero');
 $table->string('complemento')->nullable();
 $table->string('bairro');
 $table->string('cidade');
 $table->string('estado', 2);
 $table->string('banco')->nullable();
 $table->string('agencia')->nullable();
 $table->string('conta')->nullable();
 $table->string('pix')->nullable();
 $table->enum('tipo_veiculo', ['moto', 'carro', 'bicicleta', 'pe'])->default('moto');
 $table->string('marca_veiculo')->nullable();
 $table->string('modelo_veiculo')->nullable();
 $table->string('placa_veiculo')->nullable();
 $table->string('cor_veiculo')->nullable();
 $table->year('ano_veiculo')->nullable();
 $table->string('cnh_numero')->nullable();
 $table->enum('cnh_categoria', ['A', 'B', 'AB', 'C', 'D', 'E'])->nullable();
 $table->date('cnh_validade')->nullable();
 $table->string('foto_cnh')->nullable();
 $table->string('foto_rg')->nullable();
 $table->string('foto_cpf')->nullable();
 $table->string('foto_comprovante_endereco')->nullable();
 $table->string('foto_entregador')->nullable();
 $table->enum('status', ['pendente', 'aprovado', 'reprovado', 'suspenso', 'ativo', 'inativo'])->default('pendente');
 $table->enum('tipo', ['interno', 'externo'])->default('externo');
 $table->text('observacoes_aprovacao')->nullable();
 $table->timestamp('data_aprovacao')->nullable();
 $table->unsignedBigInteger('aprovado_por')->nullable();
 $table->decimal('avaliacao_media', 3, 2)->default(0);
 $table->integer('total_avaliacoes')->default(0);
 $table->integer('entregas_realizadas')->default(0);
 $table->decimal('taxa_sucesso', 5, 2)->default(0);
 $table->boolean('disponivel')->default(false);
 $table->timestamp('ultimo_login')->nullable();
 $table->json('localizacao_atual')->nullable();
 $table->string('device_token')->nullable();
 $table->boolean('notificacoes_push')->default(true);
 $table->decimal('raio_entrega_km', 5, 2)->default(10.00);
 $table->foreign('aprovado_por')->references('id')->on('usuarios')->onDelete('set null');
 $table->timestamps();
 $table->index(['status', 'disponivel']);
 $table->index(['tipo', 'status']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('entregadores');
 }
}