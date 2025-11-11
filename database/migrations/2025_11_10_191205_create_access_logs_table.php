<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('email')->nullable();
            $table->string('action'); // login, logout, access_denied, etc.
            $table->string('endpoint')->nullable(); // API endpoint acessado
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Dados adicionais
            $table->enum('status', ['success', 'failed', 'denied'])->default('success');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->index(['usuario_id', 'action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
}
