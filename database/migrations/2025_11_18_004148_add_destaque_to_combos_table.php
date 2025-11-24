<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDestaqueToCombosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->boolean('destaque')->default(false)->after('ativo');
        });
    }

    public function down()
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn('destaque');
        });
    }
}
