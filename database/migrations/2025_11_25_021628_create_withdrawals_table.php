<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_code');
            $table->decimal('valor', 10, 2);
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'processado'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->string('metodo_pagamento')->default('transferencia'); // transferencia, pix
            $table->json('dados_bancarios')->nullable(); // banco, agencia, conta, pix_key
            $table->unsignedBigInteger('aprovado_por')->nullable(); // user_id do admin
            $table->timestamp('data_solicitacao')->default(now());
            $table->timestamp('data_aprovacao')->nullable();
            $table->timestamp('data_processamento')->nullable();
            $table->string('comprovante')->nullable(); // path do comprovante
            $table->timestamps();
            
            $table->index('tenant_code');
            $table->index('status');
            $table->index('data_solicitacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawals');
    }
}
