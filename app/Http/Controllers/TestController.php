<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataforuser;
use App\Helpers\UserStatusHelper;

class TestController extends Controller
{
    public function testOnlineStatus()
    {
        // Get all users to test their status
        $users = Dataforuser::all();
        
        $results = [];
        
        foreach ($users as $user) {
            // Test the UserStatusHelper
            $status = UserStatusHelper::getUserStatus($user->last_seen);
            
            $results[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'last_seen' => $user->last_seen ? $user->last_seen->format('Y-m-d H:i:s') : 'null',
                'is_online' => $user->is_online,
                'status_helper' => $status,
                'is_online_method' => $user->isOnline(),
            ];
        }
        
        return response()->json([
            'test_results' => $results,
            'total_users' => count($results),
            'current_time' => now()->format('Y-m-d H:i:s')
        ]);
    }
    
    public function updateUserStatus($userId)
    {
        $user = Dataforuser::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }
        
        // Update user's last_seen and online status
        $user->update([
            'last_seen' => now(),
            'is_online' => true
        ]);
        
        // Test the status after update
        $status = UserStatusHelper::getUserStatus($user->last_seen);
        
        return response()->json([
            'user_updated' => true,
            'user_id' => $user->id,
            'name' => $user->name,
            'last_seen' => $user->last_seen->format('Y-m-d H:i:s'),
            'is_online' => $user->is_online,
            'status_helper' => $status,
        ]);
    }
}
