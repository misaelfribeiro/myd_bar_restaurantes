<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddObservacoesToComissoesAndBonusTables extends Migration
{
 public function up()
 {
 Schema::table('comissoes', function (Blueprint $table) {
 $table->text('observacoes')->nullable();
 });
 Schema::table('bonus', function (Blueprint $table) {
 $table->text('observacoes')->nullable();
 });
 }
 public function down()
 {
 Schema::table('comissoes', function (Blueprint $table) {
 $table->dropColumn('observacoes');
 });
 Schema::table('bonus', function (Blueprint $table) {
 $table->dropColumn('observacoes');
 });
 }
}