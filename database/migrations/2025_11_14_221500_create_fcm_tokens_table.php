<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateFcmTokensTable extends Migration
{
 public function up(): void
 {
 Schema::create('fcm_tokens', function (Blueprint $table) {
 $table->id();
 $table->unsignedBigInteger('user_id')->nullable();
 $table->string('token', 255);
 $table->string('device_type')->nullable();
 $table->string('device_id')->nullable();
 $table->boolean('ativo')->default(true);
 $table->timestamps();
 $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
 $table->unique(['token']);
 });
 }
 public function down()
 {
 Schema::dropIfExists('fcm_tokens');
 }
}