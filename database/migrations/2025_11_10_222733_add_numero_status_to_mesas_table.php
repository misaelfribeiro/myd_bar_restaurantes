<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddNumeroStatusToMesasTable extends Migration
{
 public function up()
 {
 Schema::table('mesas', function (Blueprint $table) {
 $table->integer('numero')->nullable()->after('id');
 $table->integer('capacidade')->default(4);
 $table->enum('status', ['disponivel', 'ocupada', 'reservada'])->default('disponivel');
 });
 $mesas = DB::table('mesas')->get();
 foreach ($mesas as $index => $mesa) {
 DB::table('mesas')
 ->where('id', $mesa->id)
 ->update(['numero' => $index + 1]);
 }
 Schema::table('mesas', function (Blueprint $table) {
 $table->unique('numero');
 });
 }
 public function down()
 {
 Schema::table('mesas', function (Blueprint $table) {
 $table->dropColumn(['numero', 'capacidade', 'status']);
 });
 }
}