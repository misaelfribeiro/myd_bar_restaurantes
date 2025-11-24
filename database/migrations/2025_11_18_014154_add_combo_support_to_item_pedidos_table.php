<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComboSupportToItemPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('combo_id')->nullable()->after('produto_id');
            $table->enum('tipo_item', ['produto', 'combo'])->default('produto')->after('combo_id');
            
            $table->foreign('combo_id')->references('id')->on('combos')->onDelete('cascade');
            
            // Tornar produto_id nullable já que agora pode ser combo
            $table->unsignedBigInteger('produto_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_pedidos', function (Blueprint $table) {
            $table->dropForeign(['combo_id']);
            $table->dropColumn(['combo_id', 'tipo_item']);
        });
    }
}
