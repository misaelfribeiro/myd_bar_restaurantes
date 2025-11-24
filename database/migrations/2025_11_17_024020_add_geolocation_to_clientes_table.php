<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeolocationToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('estado', 2)->nullable()->after('endereco_cep');
            $table->decimal('latitude', 10, 8)->nullable()->after('estado');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Índice para busca geográfica
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['estado', 'latitude', 'longitude']);
        });
    }
}
