<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddRememberTokenToUsuariosTable extends Migration
{
 public function up()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->rememberToken();
 });
 }
 public function down()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->dropRememberToken();
 });
 }
}