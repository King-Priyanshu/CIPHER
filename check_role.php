<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(1);
echo "User: " . $user->name . "\n";
echo "Role: " . ($user->role ? $user->role->slug : 'None') . "\n";
echo "Has Role 'admin': " . ($user->hasRole('admin') ? 'Yes' : 'No') . "\n";
echo "Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
