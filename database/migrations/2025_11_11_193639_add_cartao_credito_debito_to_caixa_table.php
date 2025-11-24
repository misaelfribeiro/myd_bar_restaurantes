<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCartaoCreditoDebitoToCaixaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caixa', function (Blueprint $table) {
            // Adicionar colunas separadas para cartão crédito e débito
            $table->decimal('total_cartao_credito', 10, 2)->default(0)->after('total_dinheiro');
            $table->decimal('total_cartao_debito', 10, 2)->default(0)->after('total_cartao_credito');
            
            // Atualizar a coluna total_cartao para ser a soma de crédito + débito
            // Vamos manter a coluna para compatibilidade
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caixa', function (Blueprint $table) {
            $table->dropColumn(['total_cartao_credito', 'total_cartao_debito']);
        });
    }
}
