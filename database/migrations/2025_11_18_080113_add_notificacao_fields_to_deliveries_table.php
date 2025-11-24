<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificacaoFieldsToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->integer('tentativas_notificacao')->default(0)->after('valor_entregador');
            $table->timestamp('ultima_notificacao_em')->nullable()->after('tentativas_notificacao');
            $table->json('entregadores_notificados')->nullable()->after('ultima_notificacao_em');
            $table->integer('raio_busca_km')->default(5)->after('entregadores_notificados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'tentativas_notificacao',
                'ultima_notificacao_em',
                'entregadores_notificados',
                'raio_busca_km'
            ]);
        });
    }
}
