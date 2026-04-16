<?php

namespace App\Http\Controllers;

use App\Models\Message; // تأكد أن هذا المسار صحيح
use App\Models\Dataforuser; // تأكد أن هذا المسار صحيح
use App\Events\MessageSent;
use App\Helpers\UserStatusHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // عرض صندوق الوارد
    public function inbox()
    {
        $myId = session('user_id');

        // الحماية: لو السيشن ضاعت ارجعه للهوم
        if (!$myId) {
            return redirect('/')->with('error', 'يجب تسجيل الدخول');
        }

        // جلب المحادثات (آخر رسالة من كل شخص)
        // استخدمنا get() ثم unique لضمان عدم تكرار الشخص
        $conversations = Message::where('sender_id', $myId)
            ->orWhere('receiver_id', $myId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($message) use ($myId) {
                return $message->sender_id == $myId ? $message->receiver_id : $message->sender_id;
            });

        return view('inbox', compact('conversations', 'myId'));
    }

    // عرض صفحة الشات مع شخص معين
    public function index($receiverId)
    {
        $myId = session('user_id');

        if ($myId == $receiverId) {
            return redirect()->back()->with('error', 'لا يمكنك مراسلة نفسك.');
        }

        // تحديث الرسائل لتمت قراءتها
        Message::where('sender_id', $receiverId)
            ->where('receiver_id', $myId)
            ->update(['is_read' => true]);

        $receiver = Dataforuser::findOrFail($receiverId);

        $messages = Message::with('sender')->where(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        $currentUser = Dataforuser::find($myId);
        
        // Check if current user exists
        if (!$currentUser) {
            return redirect('/')->with('error', 'المستخدم غير موجود');
        }
        
        $firstMessage = Message::where(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->first();

        $canReply = true;
        if ($firstMessage && $firstMessage->sender_id == $receiverId && !$currentUser->is_subscribed && !$currentUser->is_admin) {
            $canReply = false;
        }

        // Prepare receiver status using UserStatusHelper
        $receiverStatus = UserStatusHelper::getUserStatus($receiver->last_seen);

        return view('chat', compact('receiver', 'messages', 'canReply', 'currentUser', 'receiverStatus'));
    }

    // إرسال رسالة
    public function sendMessage(Request $request)
    {
        $myId = session('user_id');
        $receiverId = $request->receiver_id;

        if ($myId == $receiverId) {
            return response()->json(['error' => 'Action forbidden'], 403);
        }

        $sender = Dataforuser::find($myId);

        if (!$sender) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // التحقق إذا كان المستخدم الحالي هو الطرف الثاني في المحادثة (أي لم يبدأ المحادثة أولاً)
        $firstMessage = Message::where(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->first();

        if ($firstMessage && $firstMessage->sender_id == $receiverId && !$sender->is_subscribed && !$sender->is_admin) {
            return response()->json(['error' => 'يجب أن تكون مشتركًا للرد على الرسائل'], 403);
        }

        // Encrypt message if user is not subscribed
        $isEncrypted = !$sender->is_subscribed && !$sender->is_admin;
        $messageBody = $request->message;
        
        if ($isEncrypted) {
            $messageBody = Crypt::encryptString($messageBody);
        }

        $message = Message::create([
            'sender_id'   => $myId,
            'receiver_id' => $receiverId,
            'body'        => $messageBody,
            'is_read'     => false,
            'is_encrypted' => $isEncrypted
        ]);

        // Create notification for receiver
        NotificationController::createNotification(
            $message->id,
            $myId,
            $receiverId,
            substr($request->message, 0, 50) . (strlen($request->message) > 50 ? '...' : '')
        );

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            Log::error("Broadcasting Error: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function fetchMessages($receiverId)
    {
        $myId = session('user_id');
        
        // Force fresh data from database - bypass all cache
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($myId, $receiverId) {
                $query->where('sender_id', $myId)->where('receiver_id', $receiverId);
            })->orWhere(function ($query) use ($myId, $receiverId) {
                $query->where('sender_id', $receiverId)->where('receiver_id', $myId);
            })->orderBy('created_at', 'asc')->get();

        // Add can_read property to each message and handle encryption
        $messages->each(function ($message) use ($myId) {
            $currentUser = \App\Models\Dataforuser::find($myId);
            $canRead = false;
            
            // Check if user can read this message
            if ($message->sender_id == $myId) {
                // User can always read their own messages
                $canRead = true;
                // Decrypt if it's their own encrypted message
                if ($message->is_encrypted) {
                    try {
                        $message->body = Crypt::decryptString($message->body);
                        $message->is_encrypted = false;
                    } catch (\Exception $e) {
                        $message->body = '[Error decrypting message]';
                    }
                }
            } else {
                // User can read received messages only if subscribed or admin
                $canRead = $currentUser && ($currentUser->is_subscribed || $currentUser->is_admin);
                
                // If user can read and message is encrypted, decrypt it
                if ($canRead && $message->is_encrypted) {
                    try {
                        $message->body = Crypt::decryptString($message->body);
                        $message->is_encrypted = false;
                    } catch (\Exception $e) {
                        $message->body = '[Error decrypting message]';
                    }
                }
            }
            
            $message->can_read = $canRead;
            
            // If user can't read, show placeholder
            if (!$canRead) {
                $message->body = 'تواصل مع الادمن لفك التشفير و اكمال المحادثه 962782941878';
            }
        });

        return response()->json([
            'messages' => $messages,
            'notifications' => Message::getUnreadNotifications(session('user_id'))
        ]);
    }

    // ============ تسجيل دخول المستخدم العادي ============
    public function showUserLogin()
    {
        return view('user-login');
    }

    public function loginUser(Request $request)
    {
        $user = \App\Models\Dataforuser::where('email', $request->email)->first();

        // التأكد من وجود المستخدم والتحقق من كلمة المرور بشكل آمن
        if ($user && Hash::check($request->password, $user->password)) {
            // التأكد إنه مو مسؤول
            if ($user->is_admin) {
                return back()->with('error', '❌ هذا الحساب مخصص للمسؤولين فقط. استخدم صفحة تسجيل دخول المسؤول.');
            }

            // Use Laravel Auth system
            Auth::login($user);
            
            // Also set session as backup
            session(['user_id' => $user->id]);
            session()->regenerate();

            // Check if there's a target user from guest search
            $targetUserId = session('target_user_id');
            if ($targetUserId) {
                session()->forget('target_user_id');
                return redirect()->route('chat.index', $targetUserId)->with('success', '✅ تم تسجيل الدخول بنجاح!');
            }

            return redirect()->route('chat.inbox')->with('success', '✅ تم تسجيل الدخول بنجاح!');
        }

        return back()->with('error', '❌ بيانات الدخول غير صحيحة')->withInput();
    }

    // ============ تسجيل دخول المسؤول ============
    public function showAdminLogin()
    {
        return view('admin-login');
    }

    public function loginAdmin(Request $request)
    {
        $user = \App\Models\Dataforuser::where('email', $request->email)->first();

        // التأكد من وجود المستخدم والتحقق من كلمة المرور بشكل آمن
        if ($user && Hash::check($request->password, $user->password)) {
            // التحقق إنه مسؤول فعلاً
            if (!$user->is_admin) {
                return back()->with('error', '❌ هذا الحساب ليس مخول للدخول كمسؤول. استخدم تسجيل الدخول العادي.');
            }

            session(['user_id' => $user->id]); // هنا "تذكرة الدخول" اتحطت في السيشن
            session()->regenerate(); // تجديد السيشن عشان الأمان

            return redirect()->route('admin.dashboard')->with('success', '✅ تم تسجيل الدخول كمسؤول بنجاح!');
        }

        return back()->with('error', '❌ بيانات الدخول غير صحيحة')->withInput();
    }

    // ============ الخروج من الحساب ============
    public function logout()
    {
        session()->forget('user_id'); // مسح التذكرة عند الخروج
        session()->regenerate(); // تجديد السيشن
        return redirect('/')->with('success', '👋 تم تسجيل الخروج بنجاح');
    }

    // ============ Get User Status ============
    public function getUserStatus($userId)
    {
        $user = Dataforuser::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        return response()->json([
            'status' => $user->isOnline() ? 'online' : 'offline',
            'last_seen' => $user->last_seen,
            'last_seen_arabic' => $user->last_seen_text,
            'user_id' => $user->id,
            'name' => $user->name
        ]);
    }
}
