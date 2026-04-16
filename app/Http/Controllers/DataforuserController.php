<?php

namespace App\Http\Controllers;

use App\Models\Dataforuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DataforuserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:dataforusers,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'gender' => 'required',
            'age' => 'required|integer',
            'country' => 'required',
            'city' => 'required',
        ]);

        // 1. Hifz albyanat (bidun basurud)
        $user = Dataforuser::create([
            'name'        => $request->name,
            'gender'      => $request->gender,
            'age'         => $request->age,
            'country'     => $request->country,
            'city'        => $request->city,
            'height'      => $request->height,
            'weight'      => $request->weight,
            'skin_color'  => $request->skin_color,
            'status'      => $request->status,
            'education'   => $request->education,
            'job'         => $request->job,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'is_subscribed' => false,
            'is_admin'    => false,
        ]);

        // 2. Simple session only - no Auth, no middleware issues
        session(['user_id' => $user->id]);

        // Check if there's a target user from guest search
        $targetUserId = session('target_user_id');
        
        // Debug: Log the target user ID
        \Log::info('Target User ID from session: ' . $targetUserId);
        \Log::info('All session data: ' . json_encode(session()->all()));
        
        if ($targetUserId) {
            session()->forget('target_user_id');
            return redirect()->route('chat.index', $targetUserId)->with('success', 'User registered successfully!');
        }

        // 3. Altawjuh li safat albahth
        return redirect()->route('search.index')->with('success', 'User registered successfully!');
    }

    public function search()
    {
        // 1. Luqar almustakhdim min qayat albyanat bistikhdam al- ID almakhtuzin fi alsiyon
        $userId = session('user_id');
        
        // Debug: Check session
        if (!$userId) {
            return redirect()->route('user.login')->with('error', 'Session lost, please login again');
        }
        
        $user = Dataforuser::find($userId);

        // 2. Hima'at alsafha: Luu mfiush mustakhdim fi alsiyon yarj'uh lilhom
        if (!$user) {
            session()->forget('user_id');
            return redirect()->route('user.login')->with('error', 'User not found, please login again');
        }

        // 3. تحديد الجنس المستهدف (عكس جنس المستخدم)
        if ($user->gender == 'male') {
            $targetGender = 'female';
        } else {
            $targetGender = 'male';
        }

        // 4. جلب النتائج
        $results = Dataforuser::where('gender', $targetGender)->where('id', '!=', $userId)->where('is_admin', false)->get();

        // Add online status and last seen to each result
        $results->each(function ($user) {
            $user->is_online = $user->isOnline();
            $user->last_seen_text = $user->last_seen_text;
        });

        return view('search_results', compact('results'));
    }
}
