<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePlanosContratosTable extends Migration
{
 public function up()
 {
 Schema::create('planos', function (Blueprint $table) {
 $table->id();
 $table->string('codigo', 20)->unique();
 $table->string('nome', 100);
 $table->text('descricao')->nullable();
 $table->decimal('valor_mensal', 10, 2)->default(0);
 $table->decimal('valor_anual', 10, 2)->default(0);
 $table->decimal('desconto_anual_percent', 5, 2)->default(0);
 $table->integer('max_usuarios')->default(5);
 $table->integer('max_produtos')->default(100);
 $table->integer('max_pedidos_mes')->default(500);
 $table->integer('max_filiais')->default(1);
 $table->integer('max_mesas')->default(20);
 $table->integer('max_entregadores')->default(5);
 $table->decimal('taxa_transacao_percent', 5, 2)->default(0);
 $table->decimal('taxa_fixa_pedido', 10, 2)->default(0);
 $table->decimal('taxa_delivery_percent', 5, 2)->default(0);
 $table->json('recursos')->nullable();
 $table->integer('dias_trial')->default(30);
 $table->boolean('permite_trial')->default(true);
 $table->boolean('ativo')->default(true);
 $table->integer('ordem')->default(0);
 $table->boolean('destaque')->default(false);
 $table->timestamps();
 });
 Schema::create('contratos', function (Blueprint $table) {
 $table->id();
 $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
 $table->foreignId('plano_id')->constrained('planos')->onDelete('restrict');
 $table->string('numero_contrato', 50)->unique();
 $table->date('data_inicio');
 $table->date('data_fim');
 $table->date('data_renovacao')->nullable();
 $table->enum('tipo_pagamento', ['mensal', 'anual'])->default('mensal');
 $table->decimal('valor_contratado', 10, 2);
 $table->decimal('desconto_aplicado', 10, 2)->default(0);
 $table->decimal('valor_final', 10, 2);
 $table->integer('max_usuarios')->nullable();
 $table->integer('max_produtos')->nullable();
 $table->integer('max_pedidos_mes')->nullable();
 $table->integer('max_filiais')->nullable();
 $table->enum('status', ['ativo', 'trial', 'suspenso', 'cancelado', 'vencido'])->default('trial');
 $table->text('observacoes')->nullable();
 $table->string('documento_assinado')->nullable();
 $table->string('documento_identidade')->nullable();
 $table->string('comprovante_endereco')->nullable();
 $table->date('data_cancelamento')->nullable();
 $table->text('motivo_cancelamento')->nullable();
 $table->foreignId('cancelado_por')->nullable()->constrained('users')->onDelete('set null');
 $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
 $table->foreignId('aprovado_por')->nullable()->constrained('users')->onDelete('set null');
 $table->timestamp('aprovado_em')->nullable();
 $table->timestamps();
 $table->softDeletes();
 $table->index(['empresa_id', 'status']);
 $table->index('data_fim');
 });
 Schema::create('historico_contratos', function (Blueprint $table) {
 $table->id();
 $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
 $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
 $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
 $table->string('acao', 50);
 $table->text('descricao');
 $table->json('dados_anteriores')->nullable();
 $table->json('dados_novos')->nullable();
 $table->string('ip_address', 45)->nullable();
 $table->timestamps();
 $table->index(['contrato_id', 'created_at']);
 });
 Schema::create('faturas', function (Blueprint $table) {
 $table->id();
 $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
 $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
 $table->string('numero_fatura', 50)->unique();
 $table->date('data_referencia');
 $table->date('data_vencimento');
 $table->date('data_emissao');
 $table->decimal('valor_plano', 10, 2);
 $table->decimal('valor_adicional', 10, 2)->default(0);
 $table->decimal('desconto', 10, 2)->default(0);
 $table->decimal('valor_total', 10, 2);
 $table->json('itens_adicionais')->nullable();
 $table->enum('status', ['pendente', 'pago', 'vencido', 'cancelado'])->default('pendente');
 $table->date('data_pagamento')->nullable();
 $table->string('forma_pagamento', 50)->nullable();
 $table->string('comprovante_pagamento')->nullable();
 $table->string('chave_nfe')->nullable();
 $table->string('numero_nfe')->nullable();
 $table->string('arquivo_nfe')->nullable();
 $table->text('observacoes')->nullable();
 $table->timestamps();
 $table->index(['empresa_id', 'status']);
 $table->index('data_vencimento');
 });
 }
 public function down()
 {
 Schema::dropIfExists('faturas');
 Schema::dropIfExists('historico_contratos');
 Schema::dropIfExists('contratos');
 Schema::dropIfExists('planos');
 }
}