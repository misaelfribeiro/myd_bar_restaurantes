<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddTenantFieldsToEmpresasTable extends Migration
{
 public function up()
 {
 Schema::table('empresas', function (Blueprint $table) {
 $table->boolean('is_master')->default(false)->after('tipo');
 $table->string('tenant_code', 20)->nullable()->unique()->after('is_master');
 $table->enum('plano', ['basico', 'profissional', 'premium', 'enterprise'])->default('basico')->after('tenant_code');
 $table->date('data_inicio_contrato')->nullable()->after('plano');
 $table->date('data_fim_contrato')->nullable()->after('data_inicio_contrato');
 $table->enum('status_contrato', ['ativo', 'suspenso', 'cancelado', 'trial'])->default('trial')->after('data_fim_contrato');
 $table->integer('max_usuarios')->default(5)->after('status_contrato');
 $table->integer('max_produtos')->default(100)->after('max_usuarios');
 $table->integer('max_pedidos_mes')->default(500)->after('max_produtos');
 $table->integer('max_filiais')->default(1)->after('max_pedidos_mes');
 $table->decimal('valor_mensalidade', 10, 2)->default(0)->after('max_filiais');
 $table->decimal('taxa_transacao_percent', 5, 2)->default(0)->after('valor_mensalidade');
 $table->decimal('taxa_fixa_pedido', 10, 2)->default(0)->after('taxa_transacao_percent');
 $table->string('dominio_personalizado')->nullable()->after('taxa_fixa_pedido');
 $table->string('cor_primaria', 7)->default('#6366f1')->after('dominio_personalizado');
 $table->string('cor_secundaria', 7)->default('#8b5cf6')->after('cor_primaria');
 $table->boolean('permite_white_label')->default(false)->after('cor_secundaria');
 $table->json('recursos_habilitados')->nullable()->after('permite_white_label'); 
 $table->index('tenant_code');
 $table->index('plano');
 $table->index('status_contrato');
 $table->index('is_master');
 });
 }
 public function down()
 {
 Schema::table('empresas', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropIndex(['plano']);
 $table->dropIndex(['status_contrato']);
 $table->dropIndex(['is_master']);
 $table->dropColumn([
 'is_master',
 'tenant_code',
 'plano',
 'data_inicio_contrato',
 'data_fim_contrato',
 'status_contrato',
 'max_usuarios',
 'max_produtos',
 'max_pedidos_mes',
 'max_filiais',
 'valor_mensalidade',
 'taxa_transacao_percent',
 'taxa_fixa_pedido',
 'dominio_personalizado',
 'cor_primaria',
 'cor_secundaria',
 'permite_white_label',
 'recursos_habilitados'
 ]);
 });
 }
}