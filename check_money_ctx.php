<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ctx = DB::table('ai_contexts')->where('key', 'select_payment_money')->first();
echo "Active: {$ctx->active}\n";
echo "Pattern: {$ctx->pattern}\n";
echo "Action: {$ctx->action}\n";
echo "Handler Method: " . ($ctx->handler_method ?? 'null') . "\n";
echo "Parameters: {$ctx->parameters}\n";
