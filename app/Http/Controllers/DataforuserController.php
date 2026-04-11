<?php

namespace App\Http\Controllers;

use App\Models\Dataforuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // السطر ده هو اللي كان فيه المشكلة

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

        // 1. حفظ البيانات (بدون باسورود)
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

        // 2. تسجيل "معرف المستخدم" في السيشن يدوياً
        // بدلاً من Auth::login
        session(['user_id' => $user->id]);

        // 3. التوجيه لصفحة البحث
        return redirect()->route('search.index');
    }

    public function search()
    {
        // 1. جلب المستخدم من قاعدة البيانات باستخدام الـ ID المتخزن في السيشن
        $userId = session('user_id');
        $user = Dataforuser::find($userId);

        // 2. حماية الصفحة: لو مفيش مستخدم في السيشن يرجعه للهوم
        if (!$user) {
            return redirect()->route('home')->with('error', 'يرجى إدخال بياناتك أولاً');
        }

        // 3. تحديد الجنس المستهدف (عكس جنس المستخدم)
        if ($user->gender == 'male') {
            $targetGender = 'female';
        } else {
            $targetGender = 'male';
        }

        // 4. جلب النتائج
        $results = Dataforuser::where('gender', $targetGender)->where('id', '!=', $userId)->where('is_admin', false)->get();

        return view('search_results', compact('results'));
    }
}
