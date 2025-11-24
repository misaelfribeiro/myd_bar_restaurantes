<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateLicensesTable extends Migration
{
 public function up()
 {
 Schema::create('licenses', function (Blueprint $table) {
 $table->id();
 $table->string('license_key')->unique();
 $table->string('tenant_code')->index();
 $table->string('cliente_nome');
 $table->string('cliente_email');
 $table->string('cliente_documento')->nullable();
 $table->string('cliente_telefone')->nullable();
 $table->string('tipo')->default('standard');
 $table->integer('max_usuarios')->default(5);
 $table->integer('max_mesas')->default(20);
 $table->boolean('modulo_delivery')->default(false);
 $table->boolean('modulo_rh')->default(false);
 $table->boolean('modulo_financeiro')->default(false);
 $table->date('data_ativacao')->nullable();
 $table->date('data_expiracao')->nullable();
 $table->enum('status', ['ativa', 'expirada', 'suspensa', 'cancelada'])->default('ativa');
 $table->string('hardware_id')->nullable();
 $table->integer('tentativas_ativacao')->default(0);
 $table->timestamp('ultima_verificacao')->nullable();
 $table->text('observacoes')->nullable();
 $table->timestamps();
 $table->index('status');
 $table->index('data_expiracao');
 });
 }
 public function down()
 {
 Schema::dropIfExists('licenses');
 }
}