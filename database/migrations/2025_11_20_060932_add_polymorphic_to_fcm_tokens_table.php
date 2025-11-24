<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPolymorphicToFcmTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Adicionar campos polimórficos
            $table->string('tokenable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('tokenable_id')->nullable()->after('tokenable_type');
        });
        
        // Migrar dados existentes de user_id para tokenable
        DB::statement("
            UPDATE fcm_tokens 
            SET tokenable_type = 'App\\\\Models\\\\User',
                tokenable_id = user_id
            WHERE user_id IS NOT NULL
        ");
        
        Schema::table('fcm_tokens', function (Blueprint $table) {
            // Criar índice para o polimórfico
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fcm_tokens', function (Blueprint $table) {
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
            $table->dropColumn(['tokenable_type', 'tokenable_id']);
        });
    }
}
