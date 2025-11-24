<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrigemLatitudeLongitudeToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('origem_latitude', 10, 8)->nullable()->after('destino_longitude');
            $table->decimal('origem_longitude', 11, 8)->nullable()->after('origem_latitude');
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
            $table->dropColumn(['origem_latitude', 'origem_longitude']);
        });
    }
}
