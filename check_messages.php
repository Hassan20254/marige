<?php

require_once 'vendor/autoload.php';

use App\Models\Message;

echo "Checking encrypted messages in database:\n";

$messages = Message::where('is_encrypted', true)->get();
echo "Found {$messages->count()} encrypted messages\n";

foreach ($messages->take(3) as $msg) {
    echo "Message ID: {$msg->id}, Body: " . substr($msg->body, 0, 50) . "...\n";
}
