<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddTenantIdToTables extends Migration
{
 public function up()
 {
 Schema::table('users', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 Schema::table('produtos', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 Schema::table('categorias', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 Schema::table('pedidos', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 Schema::table('mesas', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 Schema::table('clientes', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 if (Schema::hasTable('deliveries')) {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 }
 if (Schema::hasTable('entregadores')) {
 Schema::table('entregadores', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 }
 if (Schema::hasTable('caixas')) {
 Schema::table('caixas', function (Blueprint $table) {
 $table->string('tenant_code', 20)->nullable()->after('id');
 $table->index('tenant_code');
 });
 }
 }
 public function down()
 {
 Schema::table('users', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 Schema::table('produtos', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 Schema::table('categorias', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 Schema::table('pedidos', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 Schema::table('mesas', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 Schema::table('clientes', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 if (Schema::hasTable('deliveries')) {
 Schema::table('deliveries', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 }
 if (Schema::hasTable('entregadores')) {
 Schema::table('entregadores', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 }
 if (Schema::hasTable('caixas')) {
 Schema::table('caixas', function (Blueprint $table) {
 $table->dropIndex(['tenant_code']);
 $table->dropColumn('tenant_code');
 });
 }
 }
}