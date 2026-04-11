<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session()->has('user_id'))
        @php $currentUser = \App\Models\Dataforuser::find(session('user_id')); @endphp
        @if ($currentUser && $currentUser->is_admin)
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('chat.inbox') }}">رسائلي</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="{{ route('logout') }}">خروج</a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.login') }}" style="color: #00FFE6;">🔑 تسجيل دخول</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.login') }}" style="color: #FFD700;">👨‍💼 مسؤول</a>
        </li>
    @endif
    @yield('content')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
