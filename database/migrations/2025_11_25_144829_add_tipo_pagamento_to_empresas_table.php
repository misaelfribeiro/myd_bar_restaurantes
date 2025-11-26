<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPagamentoToEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->enum('tipo_recebimento_pagamento', ['automatico', 'manual'])
                ->default('manual')
                ->after('taxa_servico_plataforma')
                ->comment('Define se cliente paga direto via MP (automatico) ou tradicional (manual)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('tipo_recebimento_pagamento');
        });
    }
}
