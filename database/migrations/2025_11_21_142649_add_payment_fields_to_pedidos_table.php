<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Verifica se troco_para não existe antes de adicionar
            if (!Schema::hasColumn('pedidos', 'troco_para')) {
                $table->decimal('troco_para', 10, 2)->nullable()->after('forma_pagamento');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'troco_para')) {
                $table->dropColumn('troco_para');
            }
        });
    }
}
