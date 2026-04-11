@extends('layouts.app')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #1a237e, #283593);
            height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        /* Wrapper */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .login-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 45px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(26, 35, 126, 0.25);
            border-top: 4px solid #1a237e;
        }

        /* Title */
        .login-title {
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Icon */
        .admin-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        /* Label */
        label {
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* Inputs */
        .form-control {
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #eee;
            transition: .3s;
        }

        .form-control:focus {
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.15);
        }

        /* Button */
        .login-btn {
            background: #1a237e;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px;
            transition: .3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(26, 35, 126, 0.3);
            background: #0d1a4d;
        }

        .user-link {
            margin-top: 15px;
            text-align: center;
        }

        .user-link a {
            color: #1a237e;
            text-decoration: none;
            font-weight: 500;
        }

        .user-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 5px;
        }

        .admin-badge {
            display: inline-block;
            background-color: #1a237e;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>

    <div class="login-wrapper" dir="rtl">

        <div class="login-card text-center">

            <div class="admin-icon">👨‍💼</div>
            <h3 class="login-title">لوحة التحكم - المسؤول</h3>
            <div class="admin-badge">إدارة النظام</div>

            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div class="mb-3 text-end">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>

                <div class="mb-4 text-end">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn login-btn w-100">
                    دخول لوحة التحكم
                </button>

            </form>

            <div class="user-link">
                مستخدم عادي؟ <a href="{{ route('user.login') }}">تسجيل دخول عادي</a>
            </div>

        </div>

    </div>
@endsection
