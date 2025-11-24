<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroPedidoToPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->integer('numero_pedido')->nullable()->after('id');
            $table->index(['tenant_code', 'numero_pedido']);
        });
        
        // Gerar números sequenciais para pedidos existentes por tenant
        $tenants = DB::table('pedidos')->select('tenant_code')->distinct()->get();
        
        foreach ($tenants as $tenant) {
            $pedidos = DB::table('pedidos')
                ->where('tenant_code', $tenant->tenant_code)
                ->orderBy('id')
                ->get();
            
            $numero = 1;
            foreach ($pedidos as $pedido) {
                DB::table('pedidos')
                    ->where('id', $pedido->id)
                    ->update(['numero_pedido' => $numero]);
                $numero++;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['tenant_code', 'numero_pedido']);
            $table->dropColumn('numero_pedido');
        });
    }
}
