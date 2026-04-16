<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DataforuserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestSearchController;
use App\Http\Controllers\TestController;

// 1. Serve the main page
Route::get('/', function () {
    if (!session()->has('user_id')) {
        return view('index');
    }

    $user = \App\Models\Dataforuser::find(session('user_id'));

    if ($user && $user->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    // User is logged in, redirect to search page
    return redirect()->route('search.index');
})->middleware(['store.target_user'])->name('home');

// 1b. Home page for logged in users (with notifications)
Route::get('/home-with-notifications', function () {
    if (!session()->has('user_id')) {
        return redirect()->route('home');
    }

    $user = \App\Models\Dataforuser::find(session('user_id'));

    if ($user && $user->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return view('home');
})->middleware(['check.session'])->name('home.loggedin');

// 2. مسارات التسجيل وتسجيل الدخول (متاحة للكل)
Route::middleware(['store.target_user'])->group(function () {
    Route::post('/register', [DataforuserController::class, 'store'])->name('register.store');
    Route::post('/user/login', [ChatController::class, 'loginUser'])->name('user.login.submit');
});

// ============ بحث للزوار (بدون تسجيل) ============
Route::get('/guest-search', [GuestSearchController::class, 'index'])->name('guest.search');
Route::get('/guest-search/results', [GuestSearchController::class, 'search'])->name('guest.search.results');

// ============ page d'inscription ============
Route::get('/register', function () {
    return view('home');
})->middleware(['store.target_user'])->name('register');

// ============ connexion utilisateur normal ============
Route::get('/login', [ChatController::class, 'showUserLogin'])->name('user.login');

// ============ تسجيل دخول المسؤول ============
Route::get('/admin/login', [ChatController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [ChatController::class, 'loginAdmin'])->name('admin.login.submit');

// ============ تسجيل الخروج ============
Route::get('/logout', [ChatController::class, 'logout'])->name('logout');

// 3. Routes with session check and last seen update
Route::middleware(['check.session', 'update.last_seen'])->group(function () {
    Route::get('/search', [DataforuserController::class, 'search'])->name('search.index');
    Route::get('/inbox', [ChatController::class, 'inbox'])->name('chat.inbox');
    Route::get('/chat/{receiverId}', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{receiverId}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

Route::middleware(['check.session', 'check.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/user/{user}/toggle-subscription', [AdminController::class, 'toggleSubscription'])->name('admin.user.toggleSubscription');
    Route::post('/admin/fix-encrypted-messages', [AdminController::class, 'fixEncryptedMessages'])->name('admin.fix.encrypted');
});

// Notification routes - simple without middleware
Route::get('/get-notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
Route::get('/stream-notifications', [\App\Http\Controllers\NotificationController::class, 'streamNotifications'])->name('notifications.stream');
Route::post('/mark-notification-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark.read');
Route::post('/clear-notifications', [\App\Http\Controllers\NotificationController::class, 'clearNotifications'])->name('notifications.clear');

// Comprehensive test route for notifications
Route::get('/test-notification', function () {
    if (!session('user_id')) {
        return 'Please login first';
    }
    
    $userId = session('user_id');
    
    // Clear notification cache
    Cache::forget("user_notifications_{$userId}");
    
    // Get available users from database
    $availableUsers = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', '!=', $userId)
        ->take(3)
        ->get(['id', 'name']);
    
    if ($availableUsers->isEmpty()) {
        return 'No other users found in database to send test notification from';
    }
    
    // Use the first available user as sender
    $sender = $availableUsers->first();
    $testSenderId = $sender->id;
    $testSenderName = $sender->name;
    
    // Create test notification
    $testNotification = [
        'message_id' => 'test_' . time(),
        'sender_id' => $testSenderId,
        'receiver_id' => $userId,
        'message_preview' => "Test message from {$testSenderName}: " . date('H:i:s'),
        'is_read' => false,
        'created_at' => now()->format('Y-m-d H:i:s')
    ];
    
    // Store notification
    $notifications = Cache::get("user_notifications_{$userId}", []);
    array_unshift($notifications, $testNotification);
    Cache::put("user_notifications_{$userId}", $notifications, 1800);
    
    return response()->json([
        'success' => true,
        'message' => 'Test notification created successfully!',
        'current_user' => [
            'id' => $userId,
            'name' => \App\Models\User::find($userId)->name ?? 'Unknown'
        ],
        'sender' => [
            'id' => $testSenderId,
            'name' => $testSenderName
        ],
        'notification' => $testNotification,
        'available_senders' => $availableUsers->toArray()
    ]);
});

// Debug route to check notification data
Route::get('/debug-notifications', function () {
    if (!session('user_id')) {
        return 'Please login first';
    }
    
    $userId = session('user_id');
    $notifications = Cache::get("user_notifications_{$userId}", []);
    
    $debugInfo = [
        'user_id' => $userId,
        'user_name' => \App\Models\User::find($userId)->name ?? 'Unknown',
        'notifications_count' => count($notifications),
        'notifications' => []
    ];
    
    foreach ($notifications as $notification) {
        $senderName = 'Unknown';
        try {
            $sender = \App\Models\User::find($notification['sender_id']);
            $senderName = $sender ? $sender->name : 'User ' . $notification['sender_id'];
        } catch (\Exception $e) {
            $senderName = 'Error: ' . $e->getMessage();
        }
        
        $debugInfo['notifications'][] = [
            'id' => $notification['message_id'],
            'sender_id' => $notification['sender_id'],
            'sender_name' => $senderName,
            'message' => $notification['message_preview'],
            'is_read' => $notification['is_read'],
            'created_at' => $notification['created_at']
        ];
    }
    
    return response()->json($debugInfo);
});

// Simple debug to check users in database
Route::get('/check-users', function () {
    try {
        $users = \Illuminate\Support\Facades\DB::table('users')->take(5)->get(['id', 'name']);
        $userList = [];
        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->id,
                'name' => $user->name
            ];
        }
        return response()->json([
            'success' => true,
            'users' => $userList,
            'total_users' => \Illuminate\Support\Facades\DB::table('users')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Step 1: Check if user is logged in
Route::get('/step1-login', function () {
    return [
        'logged_in' => session('user_id') ? true : false,
        'user_id' => session('user_id'),
        'message' => session('user_id') ? 'User is logged in' : 'User is NOT logged in'
    ];
});

// Step 2: Check if there are users in database
Route::get('/step2-users', function () {
    try {
        $users = \Illuminate\Support\Facades\DB::table('users')->get(['id', 'name']);
        return [
            'success' => true,
            'total_users' => $users->count(),
            'users' => $users->take(5)->toArray()
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
});

// Step 3: Create a test notification
Route::get('/step3-create', function () {
    if (!session('user_id')) {
        return ['error' => 'Please login first'];
    }
    
    $userId = session('user_id');
    
    // Get a different user as sender
    $sender = \Illuminate\Support\Facades\DB::table('users')
        ->where('id', '!=', $userId)
        ->first();
    
    if (!$sender) {
        return ['error' => 'No other users found'];
    }
    
    $notification = [
        'message_id' => 'test_' . time(),
        'sender_id' => $sender->id,
        'receiver_id' => $userId,
        'message_preview' => "Test from {$sender->name}",
        'is_read' => false,
        'created_at' => now()->format('Y-m-d H:i:s')
    ];
    
    // Store in cache
    $notifications = Cache::get("user_notifications_{$userId}", []);
    array_unshift($notifications, $notification);
    Cache::put("user_notifications_{$userId}", $notifications, 1800);
    
    return [
        'success' => true,
        'notification' => $notification,
        'sender_name' => $sender->name
    ];
});

// Step 4: Check if notification is in cache
Route::get('/step4-cache', function () {
    if (!session('user_id')) {
        return ['error' => 'Please login first'];
    }
    
    $userId = session('user_id');
    $notifications = Cache::get("user_notifications_{$userId}", []);
    
    return [
        'cache_key' => "user_notifications_{$userId}",
        'notifications_count' => count($notifications),
        'notifications' => $notifications
    ];
});

// Step 5: Test the API endpoint
Route::get('/step5-api', function () {
    if (!session('user_id')) {
        return ['error' => 'Please login first'];
    }
    
    try {
        $controller = new \App\Http\Controllers\NotificationController();
        $response = $controller->getNotifications();
        
        return [
            'api_response' => $response->getData(),
            'status_code' => $response->getStatusCode()
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
});

// Debug session status
Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'user_id' => session('user_id'),
        'has_user_id' => session()->has('user_id'),
        'all_session_data' => session()->all(),
        'user_exists' => session('user_id') ? \App\Models\Dataforuser::find(session('user_id')) : null
    ];
});

// Test login directly
Route::get('/test-login', function () {
    // Try to find a user to test with
    $testUser = \App\Models\Dataforuser::where('is_admin', false)->first();
    
    if (!$testUser) {
        return 'No users found in database';
    }
    
    // Simulate login
    session(['user_id' => $testUser->id]);
    session()->regenerate();
    
    return [
        'message' => 'Test login successful!',
        'user_id' => session('user_id'),
        'user_name' => $testUser->name,
        'redirect_to' => route('chat.inbox')
    ];
});

// Test inbox after login
Route::get('/test-inbox', function () {
    return [
        'session_user_id' => session('user_id'),
        'has_session' => session()->has('user_id'),
        'inbox_route' => route('chat.inbox'),
        'can_access_inbox' => session()->has('user_id')
    ];
});

// Test registration flow
Route::get('/test-registration', function () {
    // Simulate creating a new user
    $testEmail = 'test' . time() . '@example.com';
    $testPassword = 'password123';
    
    // Check if user already exists
    $existingUser = \App\Models\Dataforuser::where('email', $testEmail)->first();
    if ($existingUser) {
        return [
            'message' => 'Test user already exists',
            'user_id' => $existingUser->id,
            'session_user_id' => session('user_id')
        ];
    }
    
    // Create new user
    $user = \App\Models\Dataforuser::create([
        'name' => 'Test User',
        'gender' => 'male',
        'age' => 25,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'email' => $testEmail,
        'password' => \Illuminate\Support\Facades\Hash::make($testPassword),
        'is_admin' => false,
        'is_subscribed' => false,
    ]);
    
    // Simulate login after registration
    session(['user_id' => $user->id]);
    session()->regenerate();
    
    return [
        'message' => 'Test registration successful!',
        'user_id' => $user->id,
        'user_email' => $user->email,
        'session_user_id' => session('user_id'),
        'has_session' => session()->has('user_id'),
        'redirect_to' => route('search.index'),
        'can_access_search' => true
    ];
});

// Test what happens when trying to access search page
Route::get('/test-search-access', function () {
    $userId = session('user_id');
    
    return [
        'current_session_user_id' => $userId,
        'has_user_id' => session()->has('user_id'),
        'search_route' => route('search.index'),
        'middleware_check' => 'check.session middleware will be applied',
        'user_exists_in_db' => $userId ? \App\Models\Dataforuser::find($userId) : null
    ];
});

// Comprehensive test to find the exact issue
Route::get('/comprehensive-test', function () {
    $results = [];
    
    // Test 1: Check if we can create a user
    try {
        $testEmail = 'test' . time() . '@example.com';
        $user = \App\Models\Dataforuser::create([
            'name' => 'Test User',
            'gender' => 'male',
            'age' => 25,
            'country' => 'Egypt',
            'city' => 'Cairo',
            'email' => $testEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'is_admin' => false,
            'is_subscribed' => false,
        ]);
        $results['user_creation'] = 'SUCCESS - User ID: ' . $user->id;
    } catch (\Exception $e) {
        $results['user_creation'] = 'FAILED: ' . $e->getMessage();
        return $results;
    }
    
    // Test 2: Check if we can set session
    try {
        session(['user_id' => $user->id]);
        session()->regenerate();
        $results['session_set'] = 'SUCCESS - Session user_id: ' . session('user_id');
    } catch (\Exception $e) {
        $results['session_set'] = 'FAILED: ' . $e->getMessage();
        return $results;
    }
    
    // Test 3: Check if session persists
    $results['session_persist'] = session('user_id') == $user->id ? 'SUCCESS' : 'FAILED - Session lost';
    
    // Test 4: Check if user exists in DB
    $dbUser = \App\Models\Dataforuser::find($user->id);
    $results['user_in_db'] = $dbUser ? 'SUCCESS - User found in DB' : 'FAILED - User not in DB';
    
    // Test 5: Check middleware route
    try {
        $searchRoute = route('search.index');
        $results['search_route'] = 'SUCCESS - Route exists: ' . $searchRoute;
    } catch (\Exception $e) {
        $results['search_route'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Test 6: Simulate accessing protected route
    $results['middleware_test'] = 'Session has user_id: ' . (session()->has('user_id') ? 'YES' : 'NO');
    
    return $results;
});

// Debug chat link access
Route::get('/debug-chat-link/{userId}', function ($userId) {
    $currentSession = session('user_id');
    
    return [
        'current_session_user_id' => $currentSession,
        'has_session' => session()->has('user_id'),
        'trying_to_chat_with' => $userId,
        'chat_route' => route('chat.index', $userId),
        'middleware_applied' => 'check.session middleware will be applied',
        'will_redirect_to' => $currentSession ? 'chat page' : 'login page',
        'user_exists' => \App\Models\Dataforuser::find($userId) ? 'YES' : 'NO'
    ];
});

// Test chat access directly
Route::get('/test-chat/{userId}', function ($userId) {
    if (!session('user_id')) {
        return [
            'error' => 'No session found',
            'redirect_to' => route('user.login')
        ];
    }
    
    return [
        'success' => 'Can access chat',
        'current_user' => session('user_id'),
        'chat_with' => $userId,
        'chat_link' => route('chat.index', $userId)
    ];
});

// Fix session persistence issue
Route::get('/fix-session-persistence', function () {
    // Get current session
    $currentUserId = session('user_id');
    
    if (!$currentUserId) {
        return [
            'error' => 'No active session',
            'solution' => 'Please login first'
        ];
    }
    
    // Force save session
    session()->save();
    
    // Test if session persists
    $testSession = session('user_id');
    
    return [
        'original_user_id' => $currentUserId,
        'saved_user_id' => $testSession,
        'session_persists' => $currentUserId == $testSession,
        'message' => $currentUserId == $testSession ? 'Session persistence fixed!' : 'Session still lost',
        'next_step' => 'Try accessing chat links now'
    ];
});

// Direct chat access without middleware issues
Route::get('/direct-chat/{userId}', function ($userId) {
    $currentUserId = session('user_id');
    
    if (!$currentUserId) {
        return redirect()->route('user.login')->with('error', 'Please login first');
    }
    
    // Verify both users exist
    $currentUser = \App\Models\Dataforuser::find($currentUserId);
    $targetUser = \App\Models\Dataforuser::find($userId);
    
    if (!$currentUser || !$targetUser) {
        return redirect()->route('search.index')->with('error', 'User not found');
    }
    
    // Force session save before redirect
    session()->save();
    
    return redirect()->route('chat.index', $userId);
});

// Final working solution - simple registration test
Route::get('/final-working-test', function () {
    // Create a test user
    $testEmail = 'working' . time() . '@example.com';
    $user = \App\Models\Dataforuser::create([
        'name' => 'Working Test User',
        'gender' => 'female',
        'age' => 23,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'email' => $testEmail,
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'is_admin' => false,
        'is_subscribed' => false,
    ]);
    
    // Set session directly - no Auth, no middleware
    session(['user_id' => $user->id]);
    
    return [
        'message' => 'FINAL WORKING SOLUTION',
        'user_created' => true,
        'user_id' => $user->id,
        'session_set' => session('user_id'),
        'test_search' => route('search.index'),
        'test_chat' => "/direct-chat/{$user->id}",
        'instructions' => 'Now try registering a real user - it should work!'
    ];
});

// Test Laravel Auth system
Route::get('/test-auth-system', function () {
    $results = [];
    
    // Test 1: Create user and login with Auth
    $testEmail = 'auth' . time() . '@example.com';
    $user = \App\Models\Dataforuser::create([
        'name' => 'Auth Test User',
        'gender' => 'male',
        'age' => 25,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'email' => $testEmail,
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'is_admin' => false,
        'is_subscribed' => false,
    ]);
    
    // Login using Laravel Auth
    \Illuminate\Support\Facades\Auth::login($user);
    
    $results['auth_login'] = [
        'status' => \Illuminate\Support\Facades\Auth::check() ? 'SUCCESS' : 'FAILED',
        'auth_check' => \Illuminate\Support\Facades\Auth::check(),
        'auth_user_id' => \Illuminate\Support\Facades\Auth::id(),
        'user_id' => $user->id,
        'session_user_id' => session('user_id')
    ];
    
    // Test 2: Check if Auth persists
    $originalAuthId = \Illuminate\Support\Facades\Auth::id();
    $results['auth_persistence'] = [
        'status' => $originalAuthId == $user->id ? 'SUCCESS' : 'FAILED',
        'original' => $originalAuthId,
        'expected' => $user->id
    ];
    
    // Test 3: Test direct chat access with Auth
    try {
        $directChatRoute = "/direct-chat/{$user->id}";
        $results['chat_with_auth'] = [
            'status' => \Illuminate\Support\Facades\Auth::check() ? 'SUCCESS' : 'FAILED',
            'can_access' => \Illuminate\Support\Facades\Auth::check(),
            'chat_route' => $directChatRoute
        ];
    } catch (\Exception $e) {
        $results['chat_with_auth'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
    }
    
    // Test 4: Test search access with Auth
    try {
        $searchRoute = route('search.index');
        $results['search_with_auth'] = [
            'status' => \Illuminate\Support\Facades\Auth::check() ? 'SUCCESS' : 'FAILED',
            'can_access' => \Illuminate\Support\Facades\Auth::check(),
            'search_route' => $searchRoute
        ];
    } catch (\Exception $e) {
        $results['search_with_auth'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
    }
    
    // Overall status
    $allSuccess = true;
    foreach ($results as $test => $result) {
        if ($result['status'] !== 'SUCCESS') {
            $allSuccess = false;
            break;
        }
    }
    
    $results['overall'] = [
        'status' => $allSuccess ? 'LARAVEL AUTH WORKING' : 'AUTH ISSUES FOUND',
        'ready' => $allSuccess,
        'message' => $allSuccess ? 'Try registering a new user now!' : 'Auth system needs fixes'
    ];
    
    return $results;
});

// Final comprehensive system test
Route::get('/final-system-test', function () {
    $results = [];
    
    // Test 1: Registration flow
    $testEmail = 'final' . time() . '@example.com';
    $user = \App\Models\Dataforuser::create([
        'name' => 'Final Test User',
        'gender' => 'female',
        'age' => 22,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'email' => $testEmail,
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'is_admin' => false,
        'is_subscribed' => false,
    ]);
    
    // Set session like registration does
    session(['user_id' => $user->id]);
    session()->save();
    session()->regenerate();
    
    $results['registration'] = [
        'status' => 'SUCCESS',
        'user_id' => $user->id,
        'session_set' => session('user_id') == $user->id
    ];
    
    // Test 2: Search page access
    try {
        $searchRoute = route('search.index');
        $results['search_access'] = [
            'status' => 'SUCCESS',
            'route_exists' => true,
            'session_valid' => session('user_id') == $user->id
        ];
    } catch (\Exception $e) {
        $results['search_access'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
    }
    
    // Test 3: Direct chat access
    try {
        $chatRoute = route('chat.index', $user->id);
        $directChatRoute = "/direct-chat/{$user->id}";
        $results['chat_access'] = [
            'status' => 'SUCCESS',
            'chat_route' => $chatRoute,
            'direct_chat_route' => $directChatRoute,
            'session_valid' => session('user_id') == $user->id
        ];
    } catch (\Exception $e) {
        $results['chat_access'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
    }
    
    // Test 4: Notification system
    try {
        $notificationRoute = route('notifications.get');
        $results['notifications'] = [
            'status' => 'SUCCESS',
            'route_exists' => true,
            'session_valid' => session('user_id') == $user->id
        ];
    } catch (\Exception $e) {
        $results['notifications'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
    }
    
    // Test 5: Session persistence
    $originalSession = session('user_id');
    session()->save();
    $savedSession = session('user_id');
    $results['session_persistence'] = [
        'status' => $originalSession == $savedSession ? 'SUCCESS' : 'FAILED',
        'original' => $originalSession,
        'saved' => $savedSession
    ];
    
    // Overall status
    $allSuccess = true;
    foreach ($results as $test => $result) {
        if ($result['status'] !== 'SUCCESS') {
            $allSuccess = false;
            break;
        }
    }
    
    $results['overall'] = [
        'status' => $allSuccess ? 'ALL SYSTEMS WORKING' : 'SOME ISSUES FOUND',
        'ready_for_production' => $allSuccess
    ];
    
    return $results;
});

// Fix the issue by modifying the registration redirect
Route::get('/fix-registration', function () {
    // Clear any existing session
    session()->flush();
    
    // Create a test user
    $testEmail = 'fixed' . time() . '@example.com';
    $user = \App\Models\Dataforuser::create([
        'name' => 'Fixed User',
        'gender' => 'male',
        'age' => 25,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'email' => $testEmail,
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'is_admin' => false,
        'is_subscribed' => false,
    ]);
    
    // Set session
    session(['user_id' => $user->id]);
    session()->regenerate();
    
    return [
        'message' => 'Fixed registration! Now try accessing /search',
        'user_id' => $user->id,
        'session_user_id' => session('user_id'),
        'direct_search_link' => route('search.index'),
        'instructions' => 'Click the search link above to test'
    ];
});

// Simple test to create notification with current user
Route::get('/simple-test', function () {
    if (!session('user_id')) {
        return 'Please login first';
    }
    
    $userId = session('user_id');
    $currentUser = \App\Models\User::find($userId);
    
    // Create notification from user 1 to current user (if they're different)
    $senderId = $userId == 1 ? 2 : 1;
    $sender = \App\Models\User::find($senderId);
    
    if (!$sender) {
        return 'No sender user found';
    }
    
    $notification = [
        'message_id' => 'simple_' . time(),
        'sender_id' => $senderId,
        'receiver_id' => $userId,
        'message_preview' => "Simple test from {$sender->name}",
        'is_read' => false,
        'created_at' => now()->format('Y-m-d H:i:s')
    ];
    
    // Store notification
    $notifications = Cache::get("user_notifications_{$userId}", []);
    array_unshift($notifications, $notification);
    Cache::put("user_notifications_{$userId}", $notifications, 1800);
    
    return "Simple test created! From: {$sender->name} (ID: {$senderId}) To: {$currentUser->name} (ID: {$userId})";
});

// أضف هذا السطر فقط مؤقتاً في آخر الملف
Route::get('/debug-db', function () {
    return [
        'DB_HOST' => env('DB_HOST'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'Connected' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName()
    ];
});
Route::get('/run-migrations-now', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate --force');
    return "تم بناء الجداول في قاعدة البيانات بنجاح!";
});
Route::get('/clear-all', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "Cleared cache successfully!";
});

// API endpoint for user status
Route::get('/api/user-status/{userId}', [ChatController::class, 'getUserStatus']);

// Test routes for online/offline status
Route::get('/test-online-status', [TestController::class, 'testOnlineStatus'])->name('test.online.status');
Route::get('/update-user-status/{userId}', [TestController::class, 'updateUserStatus'])->name('test.update.user.status');
Route::get('/index', function () {
    return view('index');
})->name('index');