<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioPermissoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_permissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('modulo'); // dashboard, pedidos, produtos, etc
            $table->boolean('visualizar')->default(false);
            $table->boolean('criar')->default(false);
            $table->boolean('editar')->default(false);
            $table->boolean('excluir')->default(false);
            $table->timestamps();
            
            // Índices
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->unique(['usuario_id', 'modulo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_permissoes');
    }
}
