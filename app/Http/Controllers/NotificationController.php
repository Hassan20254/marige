<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['notifications' => []]);
        }
        
        // Get notifications directly from database (no cache for simplicity)
        $notifications = \App\Models\Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'message_preview' => substr($message->message, 0, 50) . '...',
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'sender_name' => $this->getSenderName($message->sender_id),
                ];
            });
        
        return response()->json(['notifications' => $notifications]);
    }
    
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');
        $userId = session('user_id');
        
        if (!$userId || !$notificationId) {
            return response()->json(['success' => false]);
        }
        
        // Get all notifications for this user
        $notifications = Cache::get("user_notifications_{$userId}", []);
        
        // Mark the specific notification as read
        foreach ($notifications as $key => $notification) {
            if ($notification['message_id'] == $notificationId) {
                $notifications[$key]['is_read'] = true;
                break;
            }
        }
        
        // Update cache
        Cache::put("user_notifications_{$userId}", $notifications, 300);
        
        return response()->json(['success' => true]);
    }
    
    public function clearNotifications()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['success' => false]);
        }
        
        // Clear all notifications for this user
        Cache::forget("user_notifications_{$userId}");
        
        return response()->json(['success' => true]);
    }
    
    // Store new notification - called when message is sent
    public static function createNotification($messageId, $senderId, $receiverId, $messagePreview)
    {
        $notification = [
            'message_id' => $messageId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message_preview' => $messagePreview,
            'is_read' => false,
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
        
        // Get existing notifications
        $notifications = Cache::get("user_notifications_{$receiverId}", []);
        
        // Add new notification to the beginning
        array_unshift($notifications, $notification);
        
        // Keep only last 50 notifications
        $notifications = array_slice($notifications, 0, 50);
        
        // Store in cache for 30 minutes
        Cache::put("user_notifications_{$receiverId}", $notifications, 1800);
    }
    
    // Server-Sent Events for real-time notifications
    public function streamNotifications(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response('Unauthorized', 401);
        }
        
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        
        $response->setCallback(function() use ($userId) {
            $lastCount = 0;
            
            while (true) {
                $notifications = Cache::get("user_notifications_{$userId}", []);
                $unreadCount = count(array_filter($notifications, function($n) {
                    return !$n['is_read'];
                }));
                
                if ($unreadCount > $lastCount) {
                    // New notification detected
                    $latestNotification = $notifications[0] ?? null;
                    
                    if ($latestNotification) {
                        $senderName = $this->getSenderName($latestNotification['sender_id']);
                        
                        echo "data: " . json_encode([
                            'type' => 'new_notification',
                            'notification' => [
                                'id' => $latestNotification['message_id'],
                                'sender_name' => $senderName,
                                'message' => $latestNotification['message_preview'],
                                'created_at' => $latestNotification['created_at'],
                                'unread_count' => $unreadCount
                            ]
                        ]) . "\n\n";
                        
                        ob_flush();
                        flush();
                    }
                }
                
                $lastCount = $unreadCount;
                
                // Send heartbeat every 10 seconds
                echo "data: " . json_encode(['type' => 'heartbeat', 'unread_count' => $unreadCount]) . "\n\n";
                ob_flush();
                flush();
                
                // Sleep for 2 seconds
                sleep(2);
            }
        });
        
        return $response;
    }
    
    // Get sender name from database - simplified version
    private function getSenderName($senderId)
    {
        // Direct database query - most reliable
        try {
            $user = \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $senderId)
                ->first(['name']);
                
            if ($user && !empty($user->name)) {
                return $user->name;
            }
        } catch (\Exception $e) {
            // Continue to fallback
        }
        
        // Try User model as backup
        try {
            $user = \App\Models\User::find($senderId);
            if ($user && !empty($user->name)) {
                return $user->name;
            }
        } catch (\Exception $e) {
            // Continue to fallback
        }
        
        // Final fallback
        return 'User ' . $senderId;
    }
}
