<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixMesasTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remover campo numero se existir (pode estar causando problemas)
        if (Schema::hasColumn('mesas', 'numero')) {
            Schema::table('mesas', function (Blueprint $table) {
                $table->dropColumn('numero');
            });
        }
        
        // Remover campo capacidade se existir
        if (Schema::hasColumn('mesas', 'capacidade')) {
            Schema::table('mesas', function (Blueprint $table) {
                $table->dropColumn('capacidade');
            });
        }
        
        // Remover campo status se existir
        if (Schema::hasColumn('mesas', 'status')) {
            Schema::table('mesas', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
