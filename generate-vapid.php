<?php

require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

try {
    echo "Generating VAPID keys...\n\n";
    
    $keys = VAPID::createVapidKeys();
    
    echo "✅ VAPID Keys Generated Successfully!\n\n";
    echo "Add these to your .env file:\n\n";
    echo "VAPID_PUBLIC_KEY=" . $keys['publicKey'] . "\n";
    echo "VAPID_PRIVATE_KEY=" . $keys['privateKey'] . "\n";
    echo "VAPID_SUBJECT=mailto:admin@sisaraya.com\n\n";
    
    echo "Done!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
