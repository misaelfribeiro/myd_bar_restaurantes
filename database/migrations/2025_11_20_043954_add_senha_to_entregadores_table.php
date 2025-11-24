<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenhaToEntregadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            $table->string('senha')->nullable()->after('email');
        });
        
        // Adicionar senha padrão para entregadores existentes
        $entregadores = DB::table('entregadores')->whereNull('senha')->get();
        foreach ($entregadores as $entregador) {
            DB::table('entregadores')
                ->where('id', $entregador->id)
                ->update(['senha' => Hash::make('123456')]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entregadores', function (Blueprint $table) {
            $table->dropColumn('senha');
        });
    }
}
