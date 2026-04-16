<?php

namespace App\Http\Controllers;

use App\Models\Dataforuser;
use Illuminate\Http\Request;

class GuestSearchController extends Controller
{
    // Show guest search page
    public function index()
    {
        return view('guest_search');
    }

    // Show search results for guests
    public function search(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:male,female'
        ]);

        $gender = $request->gender;
        
        // Get users based on gender selection (exclude admins)
        $results = Dataforuser::where('gender', $gender)
            ->where('is_admin', false)
            ->get();

        // Add online status and last seen to each result
        $results->each(function ($user) {
            $user->is_online = $user->isOnline();
            $user->last_seen_text = $user->last_seen_text;
        });

        // Store search preference in session for later use
        session(['guest_search_gender' => $gender]);

        return view('guest_search_results', compact('results'));
    }
}
