<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Role::all()->pluck('name')->toArray();
$perms = Permission::all()->pluck('name')->toArray();

echo "ROLES:\n" . implode(', ', $roles) . "\n\n";
echo "PERMISSIONS:\n" . implode(', ', $perms) . "\n";
