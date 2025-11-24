<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Remove Maria de usuarios se existir
DB::table('usuarios')->where('email', 'admin@eatsfood.com.br')->delete();
echo "Maria removida de usuarios\n";

echo "Maria permanece em users com seu tenant_code original\n";
