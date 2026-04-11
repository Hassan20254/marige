<?php

namespace App\Http\Controllers;

use App\Models\Message; // تأكد أن هذا المسار صحيح
use App\Models\Dataforuser; // تأكد أن هذا المسار صحيح
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

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
        $firstMessage = Message::where(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->first();

        $canReply = true;
        if ($firstMessage && $firstMessage->sender_id == $receiverId && !$currentUser->is_subscribed && !$currentUser->is_admin) {
            $canReply = false;
        }

        return view('chat', compact('receiver', 'messages', 'canReply', 'currentUser'));
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

        $message = Message::create([
            'sender_id'   => $myId,
            'receiver_id' => $receiverId,
            'body'        => $request->message,
            'is_read'     => false
        ]);

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

        if (!$myId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = Message::with('sender')->where(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
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
            // تتحقق إنه مو مسؤول
            if ($user->is_admin) {
                return back()->with('error', '❌ هذا الحساب مخصص للمسؤولين فقط. استخدم صفحة تسجيل دخول المسؤول.');
            }

            session(['user_id' => $user->id]); // هنا "تذكرة الدخول" اتحطت في السيشن
            session()->regenerate(); // تجديد السيشن عشان الأمان

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
}
