<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$users = \App\Models\Dataforuser::all();
echo "USERS:\n";
foreach ($users as $u) {
    echo json_encode(['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'is_subscribed' => $u->is_subscribed, 'is_admin' => $u->is_admin]) . "\n";
}
echo "MESSAGES:\n";
foreach (\App\Models\Message::orderBy('created_at', 'asc')->get() as $m) {
    echo json_encode(['id' => $m->id, 'sender_id' => $m->sender_id, 'receiver_id' => $m->receiver_id, 'body' => $m->body, 'created_at' => $m->created_at]) . "\n";
}
