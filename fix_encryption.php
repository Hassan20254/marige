<?php

require_once 'vendor/autoload.php';

use App\Models\Message;
use Illuminate\Support\Facades\Crypt;

echo "=== Manual Encryption Fix ===\n";

// Get all encrypted messages
$encryptedMessages = Message::where('is_encrypted', true)->get();
echo "Found {$encryptedMessages->count()} encrypted messages\n";

foreach ($encryptedMessages as $message) {
    echo "\nProcessing message ID: {$message->id}\n";
    echo "Current body: " . substr($message->body, 0, 50) . "...\n";
    
    try {
        // Try to decrypt
        $decrypted = Crypt::decryptString($message->body);
        echo "Decrypted successfully: {$decrypted}\n";
        
        // Update the message
        $message->body = $decrypted;
        $message->is_encrypted = false;
        $message->save();
        
        echo "Message {$message->id} updated successfully\n";
    } catch (\Exception $e) {
        echo "Failed to decrypt: " . $e->getMessage() . "\n";
        
        // If decryption fails, just mark as unencrypted
        echo "Marking as unencrypted anyway...\n";
        $message->is_encrypted = false;
        $message->save();
    }
}

echo "\n=== Fix Complete ===\n";
