<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando contextos add_more_context e list_restaurants\n\n";

$addMore = DB::table('ai_contexts')->where('key', 'add_more_context')->first();
$listRest = DB::table('ai_contexts')->where('key', 'list_restaurants')->first();

echo "add_more_context:\n";
echo "  Pattern: {$addMore->pattern}\n";
echo "  Confidence: {$addMore->confidence_threshold}\n\n";

echo "list_restaurants:\n";
echo "  Pattern: {$listRest->pattern}\n";
echo "  Confidence: {$listRest->confidence_threshold}\n";
