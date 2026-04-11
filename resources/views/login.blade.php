@extends('layouts.app')

@section('content')

<style>

body{
    background: linear-gradient(135deg,#fff5f7,#ffe4ec);
    height:100vh;
    font-family: 'Poppins', sans-serif;
}

/* Wrapper */
.login-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Card */
.login-card{
    background:#ffffff;
    border-radius:18px;
    padding:45px;
    width:100%;
    max-width:420px;
    box-shadow:0 10px 40px rgba(216,27,96,0.15);
}

/* Title */
.login-title{
    color:#D81B60;
    font-weight:700;
    margin-bottom:25px;
}

/* Labels */
label{
    font-weight:500;
    margin-bottom:6px;
}

/* Inputs */
.form-control{
    border-radius:10px;
    padding:14px;
    border:1px solid #eee;
    transition:.3s;
}

.form-control:focus{
    border-color:#D81B60;
    box-shadow:0 0 0 3px rgba(216,27,96,0.15);
}

/* Button */
.login-btn{
    background:#D81B60;
    color:white;
    font-weight:bold;
    border-radius:10px;
    padding:12px;
    transition:.3s;
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(216,27,96,0.25);
}

</style>

<div class="login-wrapper" dir="rtl">

    <div class="login-card text-center">

        <h3 class="login-title">تسجيل الدخول</h3>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3 text-end">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-4 text-end">
                <label>كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn login-btn w-100">
                دخول
            </button>

        </form>

    </div>

</div>

@endsection