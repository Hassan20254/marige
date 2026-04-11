<?php

namespace App\Http\Controllers;

use App\Models\Dataforuser;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = Dataforuser::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('users'));
    }

    public function toggleSubscription(Request $request, Dataforuser $user)
    {
        $user->update([
            'is_subscribed' => !$user->is_subscribed,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'تم تحديث حالة الاشتراك للمستخدم بنجاح.');
    }
}
