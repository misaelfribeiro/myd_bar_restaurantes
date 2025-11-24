<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateMesasTable extends Migration
{
 public function up()
 {
 Schema::create('mesas', function (Blueprint $table) {
 $table->id();
 $table->timestamps();
 });
 }
 public function down()
 {
 Schema::dropIfExists('mesas');
 }
}