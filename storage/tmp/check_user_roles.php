<?php

use App\Models\User;

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::all();
foreach ($users as $u) {
    $roles = $u->getRoleNames()->toArray();
    echo "User: {$u->id} {$u->email} -> Roles: " . implode(', ', $roles) . "\n";
}
