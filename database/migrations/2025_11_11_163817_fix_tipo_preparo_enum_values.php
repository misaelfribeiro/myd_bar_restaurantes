<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class FixTipoPreparoEnumValues extends Migration
{
 public function up()
 {
 DB::statement("ALTER TABLE produtos MODIFY COLUMN tipo_preparo ENUM('nao_precisa', 'preparo_rapido', 'preparo_cozinha') DEFAULT 'nao_precisa'");
 }
 public function down()
 {
 DB::statement("ALTER TABLE produtos MODIFY COLUMN tipo_preparo ENUM('pronto', 'preparo') DEFAULT 'pronto'");
 }
}