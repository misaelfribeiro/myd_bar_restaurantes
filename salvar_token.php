<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('fcm_tokens')->insert([
    'user_id' => 23,
    'token' => 'czN2MULAQCmwKqSoCvuE_B:APA91bETP48PSgeHkM8lt4fvWZhIXxewGYs4-2rDDQ_YtxYtnwnrfMw-nxEy8ktK72NXYWk1xVqHJU8bTHss5qUYdvRI7OgPnu1-Qkpbmztg5L0XFq4rjw8',
    'device_type' => 'android',
    'ativo' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "\n✅ Token FCM salvo com sucesso!\n";
echo "   User ID: 23 (Cliente)\n";
echo "   Device: Android\n\n";
