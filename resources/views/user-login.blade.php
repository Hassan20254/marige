@extends('layouts.app')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #fff5f7, #ffe4ec);
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
            box-shadow: 0 10px 40px rgba(216, 27, 96, 0.15);
        }

        /* Title */
        .login-title {
            color: #D81B60;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Labels */
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
            border-color: #D81B60;
            box-shadow: 0 0 0 3px rgba(216, 27, 96, 0.15);
        }

        /* Button */
        .login-btn {
            background: #D81B60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px;
            transition: .3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(216, 27, 96, 0.25);
        }

        .admin-link {
            margin-top: 15px;
            text-align: center;
        }

        .admin-link a {
            color: #D81B60;
            text-decoration: none;
            font-weight: 500;
        }

        .admin-link a:hover {
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

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            background: white;
            padding: 0 10px;
            position: relative;
            color: #999;
            font-size: 12px;
        }

        .google-btn {
            background: white;
            color: #333;
            border: 1px solid #ddd;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            transition: .3s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .google-btn:hover {
            background: #f9f9f9;
            border-color: #D81B60;
            box-shadow: 0 5px 15px rgba(216, 27, 96, 0.15);
        }

        .google-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f0f0f0;
        }

        .google-btn:disabled:hover {
            background: #f0f0f0;
            border-color: #ddd;
            box-shadow: none;
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }

        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #856404;
            text-align: right;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>

    <div class="login-wrapper" dir="rtl">

        <div class="login-card text-center">

            <h3 class="login-title">🔑 تسجيل الدخول - المستخدم</h3>

            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session()->has('info'))
                <div class="error-message" style="background-color: #e3f2fd; color: #1976d2;">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            
            

            <form action="{{ route('user.login.submit') }}" method="POST">
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
                    دخول
                </button>

            </form>

            <div class="admin-link">
                هل أنت مسؤول؟ <a href="{{ route('admin.login') }}">تسجيل دخول مسؤول</a>
            </div>

        </div>

    </div>
@endsection
