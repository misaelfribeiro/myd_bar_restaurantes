<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAiTrainingDataForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ai_training_data', function (Blueprint $table) {
            // Remover foreign key antiga (users)
            $table->dropForeign(['user_id']);
            
            // Adicionar nova foreign key (clientes)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('clientes')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ai_training_data', function (Blueprint $table) {
            // Reverter para users
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
}
