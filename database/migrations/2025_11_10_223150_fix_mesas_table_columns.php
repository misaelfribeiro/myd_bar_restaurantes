<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class FixMesasTableColumns extends Migration
{
 public function up()
 {
 Schema::table('mesas', function (Blueprint $table) {
 if (!Schema::hasColumn('mesas', 'numero')) {
 $table->integer('numero')->unique()->after('id');
 }
 if (!Schema::hasColumn('mesas', 'capacidade')) {
 $table->integer('capacidade')->default(4)->after('numero');
 }
 if (!Schema::hasColumn('mesas', 'status')) {
 $table->enum('status', ['disponivel', 'ocupada', 'reservada'])->default('disponivel')->after('capacidade');
 }
 if (!Schema::hasColumn('mesas', 'disponivel')) {
 $table->boolean('disponivel')->default(true)->after('status');
 }
 });
 }
 public function down()
 {
 }
}