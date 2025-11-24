<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddDeletedAtToEntregadoresTable extends Migration
{
 public function up()
 {
 Schema::table('entregadores', function (Blueprint $table) {
 $table->softDeletes();
 });
 }
 public function down()
 {
 Schema::table('entregadores', function (Blueprint $table) {
 $table->dropSoftDeletes();
 });
 }
}