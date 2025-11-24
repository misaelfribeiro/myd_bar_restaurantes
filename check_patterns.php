<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$keys = ['select_payment_credit', 'select_payment_money', 'select_payment_debit', 'inform_change_amount'];
$contexts = DB::table('ai_contexts')->whereIn('key', $keys)->get();

foreach($contexts as $c) {
    echo "Key: {$c->key}\n";
    echo "Active: {$c->active}\n";
    echo "Pattern: {$c->pattern}\n";
    echo "Threshold: {$c->confidence_threshold}\n\n";
}
