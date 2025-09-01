<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test user activity tracking
$user = \App\Models\User::first();

echo "User: " . $user->name . "\n";
echo "Last seen: " . ($user->last_seen_at ? $user->last_seen_at->format('Y-m-d H:i:s') : 'Never') . "\n";
echo "Is online: " . ($user->is_online ? 'Yes' : 'No') . "\n";
echo "Activity status: " . $user->activity_status . "\n";
echo "Activity status text: " . $user->activity_status_text . "\n";

// Update the user's activity
echo "\nUpdating user activity...\n";
$user->updateLastSeen();

echo "Last seen: " . $user->last_seen_at->format('Y-m-d H:i:s') . "\n";
echo "Is online: " . ($user->is_online ? 'Yes' : 'No') . "\n";
echo "Activity status: " . $user->activity_status . "\n";
echo "Activity status text: " . $user->activity_status_text . "\n";
