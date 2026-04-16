<!DOCTYPE html>
<html lang="ar">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>موده</title>

<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

<style>

body{
    font-family:'Tajawal',sans-serif;
    background:linear-gradient(180deg,#fffaf7 0%,#fdf2f8 50%,#ffffff 100%);
    color:#111827;
}

/* BRAND */
.brand{
    font-size:28px;
    font-weight:900;
    color:#be123c;
    letter-spacing:1px;
}

/* LEFT PANEL */
.left-panel{
    background:radial-gradient(circle at top right,#ffe4e6,#fff1f2 60%,#ffffff);
    min-height:100vh;
    position:relative;
    overflow:hidden;
}

.left-panel::before{
    content:"";
    position:absolute;
    width:400px;
    height:400px;
    background:rgba(225,29,72,.08);
    border-radius:50%;
    top:-120px;
    right:-120px;
}

.left-panel h1{
    font-size:44px;
    font-weight:900;
    line-height:1.2;
}

.left-panel p{
    color:#6b7280;
    font-size:17px;
}

/* FORM CARD */
.form-card{
    background:rgba(255,255,255,.75);
    backdrop-filter:blur(18px);
    border-radius:26px;
    padding:40px;
    box-shadow:0 25px 80px rgba(0,0,0,.08);
    border:1px solid rgba(255,255,255,.6);
    transition:.3s;
}

.form-card:hover{
    transform:translateY(-2px);
}

/* INPUTS */
.form-control,
.form-select{
    border-radius:14px;
    padding:13px 14px;
    border:1px solid #e5e7eb;
    background:#fff;
    font-size:14px;
}

.form-control:focus,
.form-select:focus{
    border-color:#be123c;
    box-shadow:0 0 0 .25rem rgba(190,18,60,.12);
}

/* BUTTON */
.btn-brand{
    background:linear-gradient(135deg,#be123c,#e11d48);
    color:white;
    border:none;
    padding:14px;
    border-radius:999px;
    font-weight:700;
    letter-spacing:.3px;
    transition:.3s;
    box-shadow:0 15px 35px rgba(225,29,72,.18);
}

.btn-brand:hover{
    transform:translateY(-3px);
    box-shadow:0 20px 50px rgba(225,29,72,.25);
}

.subtitle{
    color:#6b7280;
    font-size:15px;
}

/* STATS */
.stat{
    background:rgba(255,255,255,.8);
    border-radius:18px;
    padding:18px;
    box-shadow:0 12px 30px rgba(0,0,0,.05);
}

/* ANIMATION */
.fade-up{
    animation:fadeUp .7s ease;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(15px);}
    to{opacity:1;transform:translateY(0);}
}
/* LOGIN BUTTONS */
.login-buttons{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-top:15px;
}

/* User Button */
.btn-user{
    background:#fff;
    color:#be123c;
    border:1.5px solid #fda4af;
    padding:10px 22px;
    border-radius:999px;
    font-weight:600;
    transition:.3s;
}

.btn-user:hover{
    background:#fff1f2;
    color:#9f1239;
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(225,29,72,.15);
}

/* Admin Button */
.btn-admin{
    background:linear-gradient(135deg,#be123c,#e11d48);
    color:#fff;
    padding:10px 22px;
    border-radius:999px;
    border:none;
    font-weight:600;
    transition:.3s;
}

.btn-admin:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 30px rgba(225,29,72,.25);
}

/* Guest Search Button */
.btn-outline-danger{
    background:#fff;
    color:#be123c;
    border:1.5px solid #fda4af;
    padding:10px 22px;
    border-radius:999px;
    font-weight:600;
    transition:.3s;
}

.btn-outline-danger:hover{
    background:#fff1f2;
    color:#9f1239;
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(225,29,72,.15);
}
</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- LEFT -->
<div class="col-lg-5 d-none d-lg-flex flex-column justify-content-center left-panel p-5">

<div class="fade-up">

<div class="brand mb-4" style="font-size: 2.5rem;">
موده  ❤️
</div>

<h1>
ابحث عن شريك حياتك<br>
بثقة وأمان
</h1>

<p class="mt-4">
منصة زواج حديثة تعتمد على التوافق الحقيقي والقيم المشتركة،
لتجربة أكثر أماناً وخصوصية.
</p>

<div class="mt-5 d-grid gap-3">

<div class="stat">
<strong>+50,000</strong>
<div class="text-muted small">مستخدم موثق</div>
</div>

<div class="stat">
<strong>+12,000</strong>
<div class="text-muted small">ارتباط ناجح</div>
</div>

</div>

</div>
</div>

<!-- RIGHT -->
<div class="col-lg-7 d-flex align-items-center justify-content-center p-4" style='direction: rtl;'>

<div class="form-card w-100" style="max-width:680px;">

<h2 class="fw-bold mb-2">إنشاء حساب جديد</h2>
<p class="subtitle mb-4">
ابدأ رحلتك في العثور على شريك حياتك
</p>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form action="{{ route('register.store') }}" method="POST">
@csrf

<div class="row g-3">

<div class="col-md-6">
<input type="text" name="name" class="form-control" placeholder="الاسم الكامل">
</div>

<div class="col-md-3">
<select name="gender" class="form-select">
<option>الجنس</option>
<option value="male">ذكر</option>
<option value="female">أنثى</option>
</select>
</div>

<div class="col-md-3">
<input type="number" name="age" class="form-control" placeholder="العمر">
</div>

<div class="col-md-6">
<input type="text" name="country" class="form-control" placeholder="الدولة">
</div>

<div class="col-md-6">
<input type="text" name="city" class="form-control" placeholder="المدينة">
</div>

<div class="col-md-4">
<input type="number" name="height" class="form-control" placeholder="الطول">
</div>

<div class="col-md-4">
<input type="number" name="weight" class="form-control" placeholder="الوزن">
</div>

<div class="col-md-4">
<select name="skin_color" class="form-select">
<option>لون البشرة</option>
<option>أبيض</option>
<option>قمحي</option>
<option>أسمر</option>
</select>
</div>

<div class="col-md-6">
<input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني">
</div>

<div class="col-md-6">
<input type="password" name="password" class="form-control" placeholder="كلمة المرور">
</div>

</div>

<button class="btn btn-brand w-100 mt-4">
إنشاء حسابك الآن ❤️
</button>

</form>

<div class="text-center mt-3">
<p class="small-text mb-2">لديك حساب بالفعل؟</p>

<!-- Guest Search Button -->
<div class="mb-3">
    <a href="{{ route('guest.search') }}" class="btn btn-outline-danger rounded-pill px-4 w-100">
        <i class="bi bi-search me-2"></i> ابحث كزائر
    </a>
</div>

<div class="login-buttons">

    <a href="{{ route('user.login') }}" class="btn-user">
        👤 تسجيل دخول المستخدم
    </a>

    <a href="{{ route('admin.login') }}" class="btn-admin">
        ⚙️ دخول الأدمن
    </a>

</div>

</div>

</div>
</div>

</div>
</div>

</body>
</html>