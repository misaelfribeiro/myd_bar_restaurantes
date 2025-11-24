<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateAccessLogsTable extends Migration
{
 public function up()
 {
 Schema::create('access_logs', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('usuario_id')->nullable();
 $table->string('email')->nullable();
 $table->string('action');
 $table->string('endpoint')->nullable();
 $table->string('method')->nullable();
 $table->string('ip_address');
 $table->string('user_agent')->nullable();
 $table->json('metadata')->nullable();
 $table->enum('status', ['success', 'failed', 'denied'])->default('success');
 $table->timestamps();
 $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
 $table->index(['usuario_id', 'action', 'created_at']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('access_logs');
 }
}