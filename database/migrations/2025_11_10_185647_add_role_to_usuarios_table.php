<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddRoleToUsuariosTable extends Migration
{
 public function up()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->enum('role', ['admin', 'gerente', 'garcom', 'cliente'])->default('cliente')->after('email');
 });
 }
 public function down()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->dropColumn('role');
 });
 }
}