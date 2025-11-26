<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePedidoIdNullableInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover foreign key existente
            $table->dropForeign(['pedido_id']);
            
            // Modificar coluna para nullable
            $table->unsignedBigInteger('pedido_id')->nullable()->change();
            
            // Recriar foreign key com onDelete SET NULL
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover foreign key
            $table->dropForeign(['pedido_id']);
            
            // Voltar para NOT NULL
            $table->unsignedBigInteger('pedido_id')->nullable(false)->change();
            
            // Recriar foreign key original
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
        });
    }
}
