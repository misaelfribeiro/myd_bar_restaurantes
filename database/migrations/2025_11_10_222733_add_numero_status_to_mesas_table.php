<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNumeroStatusToMesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->integer('numero')->nullable()->after('id');
            $table->integer('capacidade')->default(4)->after('lugares');
            $table->enum('status', ['disponivel', 'ocupada', 'reservada'])->default('disponivel')->after('capacidade');
        });

        // Atualizar registros existentes com números sequenciais
        $mesas = DB::table('mesas')->get();
        foreach ($mesas as $index => $mesa) {
            DB::table('mesas')
                ->where('id', $mesa->id)
                ->update(['numero' => $index + 1]);
        }

        // Adicionar constraint unique após popular os dados
        Schema::table('mesas', function (Blueprint $table) {
            $table->unique('numero');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn(['numero', 'capacidade', 'status']);
        });
    }
}
