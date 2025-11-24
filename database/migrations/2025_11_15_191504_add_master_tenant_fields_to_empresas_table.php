<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddMasterTenantFieldsToEmpresasTable extends Migration
{
 public function up()
 {
 Schema::table('empresas', function (Blueprint $table) {
 });
 }
 public function down()
 {
 Schema::table('empresas', function (Blueprint $table) {
 });
 }
}