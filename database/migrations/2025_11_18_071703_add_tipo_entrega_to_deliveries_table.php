<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoEntregaToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Tipo de entrega: fixo (entregador fixo) ou plataforma (app de entregadores)
            $table->enum('tipo_entrega', ['fixo', 'plataforma'])->default('fixo')->after('entregador_id');
            
            // Disponível para entregadores da plataforma pegarem
            $table->boolean('disponivel_plataforma')->default(false)->after('tipo_entrega');
            
            // Data/hora que foi disponibilizado na plataforma
            $table->timestamp('disponibilizado_em')->nullable()->after('disponivel_plataforma');
            
            // Data/hora que o entregador aceitou
            $table->timestamp('aceito_em')->nullable()->after('disponibilizado_em');
            
            // Valor oferecido ao entregador da plataforma
            $table->decimal('valor_entregador', 10, 2)->nullable()->after('aceito_em');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_entrega',
                'disponivel_plataforma',
                'disponibilizado_em',
                'aceito_em',
                'valor_entregador'
            ]);
        });
    }
}
