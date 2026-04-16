<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'is_read',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    // علاقة لجلب بيانات المرسل
    public function sender()
    {
        return $this->belongsTo(Dataforuser::class, 'sender_id');
    }

    // علاقة لجلب بيانات المستلم
    public function receiver()
    {
        return $this->belongsTo(Dataforuser::class, 'receiver_id');
    }

    // Get decrypted body
    public function getDecryptedBodyAttribute()
    {
        if (!$this->is_encrypted) {
            return $this->body;
        }

        try {
            return Crypt::decryptString($this->body);
        } catch (\Exception $e) {
            return '[Encrypted Message]';
        }
    }

    // Check if current user can read the message
    public function canCurrentUserRead($currentUserId)
    {
        // If message is not encrypted, anyone can read it
        if (!$this->is_encrypted) {
            return true;
        }

        // If message is encrypted, check if conversation is unlocked
        return $this->isConversationUnlocked($currentUserId);
    }

    // Check if conversation is unlocked
    private function isConversationUnlocked($currentUserId)
    {
        // Get both users in the conversation
        $sender = $this->sender;
        $receiver = $this->receiver;

        // If either user is subscribed, conversation is unlocked
        if ($sender && $sender->is_subscribed) {
            // Auto-decrypt messages if conversation is now unlocked
            $this->autoDecryptIfUnlocked();
            return true;
        }

        if ($receiver && $receiver->is_subscribed) {
            // Auto-decrypt messages if conversation is now unlocked
            $this->autoDecryptIfUnlocked();
            return true;
        }

        // If either user is admin, conversation is unlocked
        if ($sender && $sender->is_admin) {
            // Auto-decrypt messages if conversation is now unlocked
            $this->autoDecryptIfUnlocked();
            return true;
        }

        if ($receiver && $receiver->is_admin) {
            // Auto-decrypt messages if conversation is now unlocked
            $this->autoDecryptIfUnlocked();
            return true;
        }

        // If current user is part of the conversation and is the sender
        if ($this->sender_id == $currentUserId) {
            return true;
        }

        return false;
    }

    // Auto-decrypt message if conversation is unlocked
    private function autoDecryptIfUnlocked()
    {
        if ($this->is_encrypted) {
            try {
                $this->body = Crypt::decryptString($this->body);
                $this->is_encrypted = false;
                $this->save(); // Save the decrypted message to database
            } catch (\Exception $e) {
                // Keep as encrypted if decryption fails
            }
        }
    }

    // Static method to decrypt all messages for a user when they subscribe
    public static function decryptAllUserMessages($userId)
    {
        // Decrypt all messages sent by this user
        $sentMessages = Message::where('sender_id', $userId)
            ->where('is_encrypted', true)
            ->get();

        \Log::info("Found {$sentMessages->count()} encrypted messages sent by user {$userId}");

        $sentMessages->each(function ($message) use ($userId) {
            try {
                \Log::info("Decrypting sent message {$message->id} from user {$message->sender_id}");
                
                // Check if message is actually encrypted
                if (self::isActuallyEncrypted($message->body)) {
                    $decryptedBody = Crypt::decryptString($message->body);
                    $message->body = $decryptedBody;
                    $message->is_encrypted = false;
                    $message->save();
                    
                    // Clear cache and force refresh
                    \Cache::forget("messages_user_{$message->sender_id}_{$message->receiver_id}");
                    
                    \Log::info("Successfully decrypted sent message {$message->id}");
                } else {
                    // Message is not actually encrypted, just mark as unencrypted
                    \Log::info("Message {$message->id} is not actually encrypted, marking as unencrypted");
                    $message->is_encrypted = false;
                    $message->save();
                }
            } catch (\Exception $e) {
                // Keep as encrypted if decryption fails
                \Log::error('Failed to decrypt sent message ' . $message->id . ': ' . $e->getMessage());
            }
        });

        // Decrypt all messages received by this user
        $receivedMessages = Message::where('receiver_id', $userId)
            ->where('is_encrypted', true)
            ->get();

        \Log::info("Found {$receivedMessages->count()} encrypted messages received by user {$userId}");

        $receivedMessages->each(function ($message) use ($userId) {
            try {
                \Log::info("Decrypting received message {$message->id} for user {$message->receiver_id}");
                
                // Check if message is actually encrypted
                if (self::isActuallyEncrypted($message->body)) {
                    $decryptedBody = Crypt::decryptString($message->body);
                    $message->body = $decryptedBody;
                    $message->is_encrypted = false;
                    $message->save();
                    
                    // Clear cache and force refresh
                    \Cache::forget("messages_user_{$message->receiver_id}_{$message->sender_id}");
                    
                    \Log::info("Successfully decrypted received message {$message->id}");
                } else {
                    // Message is not actually encrypted, just mark as unencrypted
                    \Log::info("Message {$message->id} is not actually encrypted, marking as unencrypted");
                    $message->is_encrypted = false;
                    $message->save();
                }
            } catch (\Exception $e) {
                // Keep as encrypted if decryption fails
                \Log::error('Failed to decrypt received message ' . $message->id . ': ' . $e->getMessage());
            }
        });
    }

    // Check if a message is actually encrypted
    private static function isActuallyEncrypted($body)
    {
        try {
            // Try to decrypt - if it fails, it might be encrypted
            Crypt::decryptString($body);
            return true;
        } catch (\Exception $e) {
            // If decryption fails, check if it looks like encrypted text
            return strlen($body) > 50 && !preg_match('/^[a-zA-Z0-9\s\.\,\?\!]+$/', $body);
        }
    }

    // Static method to decrypt all messages in a conversation when user subscribes
    public static function decryptConversationMessages($userId1, $userId2)
    {
        $messages = Message::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->where('is_encrypted', true)->get();

        \Log::info("Found {$messages->count()} encrypted messages between users {$userId1} and {$userId2}");

        $messages->each(function ($message) use ($userId1, $userId2) {
            try {
                \Log::info("Decrypting message {$message->id} from user {$message->sender_id} to user {$message->receiver_id}");
                
                $decryptedBody = Crypt::decryptString($message->body);
                $message->body = $decryptedBody;
                $message->is_encrypted = false;
                $message->save();
                
                // Clear cache and force refresh
                \Cache::forget("messages_user_{$message->sender_id}_{$message->receiver_id}");
                \Cache::forget("messages_user_{$message->receiver_id}_{$message->sender_id}");
                
                \Log::info("Successfully decrypted message {$message->id}");
            } catch (\Exception $e) {
                // Keep as encrypted if decryption fails
                \Log::error('Failed to decrypt message ' . $message->id . ': ' . $e->getMessage());
            }
        });
    }

    // Encrypt message body
    public function encryptBody()
    {
        if (!$this->is_encrypted) {
            $this->body = Crypt::encryptString($this->body);
            $this->is_encrypted = true;
        }
    }

    // Create notification for new message
    public function createNotification()
    {
        $notification = [
            'message_id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message_preview' => substr($this->body, 0, 50) . '...',
            'is_read' => false,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];

        // Store notification using NotificationController
        \App\Http\Controllers\NotificationController::storeNotification($notification);
        
        // Broadcast notification via WebSocket or Pusher
        try {
            broadcast(new \App\Events\NewMessageNotification($notification))->toOthers();
        } catch (\Exception $e) {
            \Log::error('Failed to broadcast notification: ' . $e->getMessage());
        }
    }

    // Get unread notifications for user
    public static function getUnreadNotifications($userId)
    {
        return \Cache::get("notification_user_{$userId}", []);
    }

    // Mark notification as read
    public static function markNotificationAsRead($notificationId)
    {
        $notification = \Cache::get("notification_{$notificationId}");
        if ($notification) {
            $notification['is_read'] = true;
            \Cache::put("notification_{$notificationId}", $notification, 300);
        }
    }

    // Decrypt message body
    public function decryptBody()
    {
        if ($this->is_encrypted) {
            try {
                $this->body = Crypt::decryptString($this->body);
                $this->is_encrypted = false;
            } catch (\Exception $e) {
                $this->body = '[Decryption Error]';
            }
        }
    }
}