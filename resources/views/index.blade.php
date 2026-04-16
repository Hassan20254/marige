<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مودة | منصة الزواج الجاد</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>

:root{
--primary:#c08497;
--primary-soft:#f6e6ea;
--primary-dark:#a86c7f;
--bg:#faf7f8;
--glass:rgba(255,255,255,.65);
--text:#2f2f2f;
--muted:#8a8a8a;
}

body{
font-family:'Cairo',sans-serif;
background:var(--bg);
color:var(--text);
overflow-x:hidden;
}

/* ===== NAVBAR ===== */

.navbar{
background:rgba(255,255,255,.7);
backdrop-filter:blur(18px);
box-shadow:0 8px 35px rgba(0,0,0,.04);
}

.brand{
font-size:28px;
font-weight:700;
color:var(--primary);
letter-spacing:1px;
}

/* ===== HERO ===== */

.hero{
min-height:100vh;
display:flex;
align-items:center;
position:relative;
background:
linear-gradient(rgba(250,247,248,.9),rgba(250,247,248,.95)),
url("https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=1800&q=80");
background-size:cover;
background-position:center;
}

.hero::after{
content:"";
position:absolute;
width:500px;
height:500px;
background:radial-gradient(circle,#f2cbd4 0%,transparent 70%);
top:-100px;
left:-100px;
opacity:.4;
}

.hero h1{
font-size:56px;
font-weight:700;
line-height:1.3;
}

.hero p{
font-size:20px;
color:var(--muted);
max-width:650px;
margin:auto;
}

/* ===== BUTTONS ===== */

.btn-main{
background:linear-gradient(135deg,var(--primary),var(--primary-dark));
color:white;
padding:14px 38px;
border-radius:50px;
font-weight:600;
border:none;
transition:.35s;
box-shadow:0 8px 25px rgba(192,132,151,.35);
}

.btn-main:hover{
transform:translateY(-4px);
box-shadow:0 15px 40px rgba(192,132,151,.45);
}

.btn-outline-soft{
border:2px solid var(--primary);
color:var(--primary);
padding:14px 38px;
border-radius:50px;
transition:.3s;
}

.btn-outline-soft:hover{
background:var(--primary-soft);
}

/* ===== SECTIONS ===== */

.section{
padding:120px 0;
}

/* ===== GLASS CARD ===== */

.glass-card{
background:var(--glass);
backdrop-filter:blur(20px);
border-radius:24px;
padding:45px;
box-shadow:0 15px 50px rgba(0,0,0,.05);
transition:.4s;
border:1px solid rgba(255,255,255,.5);
}

.glass-card:hover{
transform:translateY(-12px) scale(1.02);
}

/* ===== STEPS ===== */

.step-number{
width:75px;
height:75px;
border-radius:50%;
background:linear-gradient(135deg,#f7d7df,#fff);
display:flex;
align-items:center;
justify-content:center;
font-size:24px;
font-weight:700;
color:var(--primary);
margin:auto;
box-shadow:0 10px 25px rgba(0,0,0,.06);
}

/* ===== CTA ===== */

.cta{
background:linear-gradient(135deg,#fff,#f8eef1);
border-radius:35px;
padding:90px 40px;
text-align:center;
box-shadow:0 15px 60px rgba(0,0,0,.05);
}

/* ===== FOOTER ===== */

footer{
padding:35px;
background:white;
text-align:center;
color:var(--muted);
margin-top:60px;
}

</style>
</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg py-3">
<div class="container">

<a class="navbar-brand brand">💍 مودة</a>

<div class="ms-auto">

<a href="{{ route('user.login') }}" class="btn btn-outline-soft me-2">
تسجيل الدخول
</a>

<a href="{{ route('register') }}" class="btn btn-main">
إنشاء حساب
</a>

</div>

</div>
</nav>


<!-- HERO -->

<section class="hero">
<div class="container text-center">

<h1>منصة راقية تساعدك على العثور<br>على شريك الحياة المناسب</h1>

<p class="mt-4">
تجربة آمنة، جادة، ومحترمة للباحثين عن الزواج والاستقرار
</p>

<div class="mt-5">

<a href="{{ route('register') }}" class="btn btn-main me-3">
ابدأ رحلتك الآن
</a>

<a href="{{ route('search.index') }}" class="btn btn-outline-soft">
تصفح الأعضاء
</a>

</div>

</div>
</section>


<!-- FEATURES -->

<section class="section">
<div class="container text-center">

<h2 class="mb-5">لماذا نحن مختلفون؟</h2>

<div class="row g-4">

<div class="col-md-4">
<div class="glass-card">
<h4>🔐 خصوصية تامة</h4>
<p>نحمي بياناتك بالكامل ونوفر بيئة آمنة للتعارف.</p>
</div>
</div>

<div class="col-md-4">
<div class="glass-card">
<h4>❤️ توافق مدروس</h4>
<p>خوارزمية ذكية لاقتراح أفضل الشركاء المحتملين.</p>
</div>
</div>

<div class="col-md-4">
<div class="glass-card">
<h4>💬 تواصل راقٍ</h4>
<p>نظام محادثات منظم يهدف للجدية والاحترام.</p>
</div>
</div>

</div>
</div>
</section>


<!-- STEPS -->

<section class="section bg-light">
<div class="container text-center">

<h2 class="mb-5">ثلاث خطوات بسيطة</h2>

<div class="row g-5">

<div class="col-md-4">
<div class="step-number">1</div>
<h5 class="mt-4">أنشئ حسابك</h5>
<p class="text-muted">سجل بياناتك وابدأ رحلتك.</p>
</div>

<div class="col-md-4">
<div class="step-number">2</div>
<h5 class="mt-4">ابحث عن التوافق</h5>
<p class="text-muted">استخدم البحث المتقدم بسهولة.</p>
</div>

<div class="col-md-4">
<div class="step-number">3</div>
<h5 class="mt-4">ابدأ التواصل</h5>
<p class="text-muted">تعارف باحترام ووضوح.</p>
</div>

</div>
</div>
</section>


<!-- CTA -->

<section class="section">
<div class="container">

<div class="cta">

<h2>قد تكون قصة حياتك على بعد خطوة واحدة ❤️</h2>

<p class="mt-4">انضم الآن وابدأ فصلًا جديدًا مليئًا بالمودة والاستقرار.</p>

<a href="{{ route('register') }}" class="btn btn-main mt-4">
إنشاء حساب مجاني
</a>

</div>

</div>
</section>


<footer>
© 2026 منصة وصال — جميع الحقوق محفوظة
</footer>

</body>
</html>