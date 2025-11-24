<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddTenantCodeToFuncionariosTable extends Migration
{
 public function up()
 {
 Schema::table('funcionarios', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->foreign('tenant_code')->references('tenant_code')->on('empresas')->onDelete('cascade');
 $table->index('tenant_code');
 });
 }
 public function down()
 {
 Schema::table('funcionarios', function (Blueprint $table) {
 $table->dropForeign(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 }
}