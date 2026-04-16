<?php

namespace App\Http\Controllers;

use App\Models\Dataforuser;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = Dataforuser::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('users'));
    }

    public function toggleSubscription($user)
    {
        $user = Dataforuser::findOrFail($user);
        $user->is_subscribed = !$user->is_subscribed;
        $user->save();

        // If user is now subscribed, decrypt all their encrypted messages
        if ($user->is_subscribed) {
            // Decrypt ALL messages for this user (both sent and received)
            \Log::info("Decrypting all messages for user {$user->id} after subscription");
            Message::decryptAllUserMessages($user->id);
        }

        return redirect()->back()->with('success', 'User subscription status updated successfully!');
    }

    // Manual fix for encrypted messages
    public function fixEncryptedMessages()
    {
        $encryptedMessages = Message::where('is_encrypted', true)->get();
        $fixedCount = 0;
        
        foreach ($encryptedMessages as $message) {
            try {
                // Try to decrypt
                $decryptedBody = Crypt::decryptString($message->body);
                $message->body = $decryptedBody;
                $message->is_encrypted = false;
                $message->save();
                $fixedCount++;
            } catch (\Exception $e) {
                // If decryption fails, just mark as unencrypted
                $message->is_encrypted = false;
                $message->save();
                $fixedCount++;
                
                // Clear all message caches
                \Cache::flush();
            }
        }
        
        return redirect()->back()->with('success', "Fixed {$fixedCount} encrypted messages!");
    }
}
