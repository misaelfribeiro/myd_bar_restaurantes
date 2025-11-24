<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddTenantCodeToUsuariosTable extends Migration
{
 public function up()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 }
 public function down()
 {
 Schema::table('usuarios', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 }
}