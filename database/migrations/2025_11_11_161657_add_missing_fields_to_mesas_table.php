<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddMissingFieldsToMesasTable extends Migration
{
 public function up()
 {
 Schema::table('mesas', function (Blueprint $table) {
 if (!Schema::hasColumn('mesas', 'identificador')) {
 $table->string('identificador')->nullable()->after('numero');
 }
 if (!Schema::hasColumn('mesas', 'lugares')) {
 $table->integer('lugares')->nullable()->after('identificador');
 }
 });
 }
 public function down()
 {
 Schema::table('mesas', function (Blueprint $table) {
 $table->dropColumn(['identificador', 'lugares']);
 });
 }
}