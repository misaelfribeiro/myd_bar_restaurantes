<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIConversationSession;

$s = AIConversationSession::orderBy('id', 'desc')->first();
if ($s) {
    echo "Session: {$s->session_token}\n";
    echo "Entities (raw JSON): {$s->getAttributes()['entities']}\n";
    echo "Entities (cast):\n";
    print_r($s->entities);
}
