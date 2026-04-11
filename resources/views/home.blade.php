<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>

<body>
    <style>
        /* الخطوط والألوان */
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .text-danger {
            color: #D81B60 !important;
        }

        .btn-danger {
            background-color: #D81B60;
            border: none;
        }

        .btn-danger:hover {
            background-color: #ad144a;
        }

        .border-dashed {
            border-style: dashed !important;
            border-color: #dee2e6 !important;
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 0.25rem rgba(216, 27, 96, 0.1);
            border: 1px solid #D81B60 !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* لجعل الصورة الجانبية ثابتة */
        @media (min-width: 992px) {
            .side-banner {
                position: fixed;
                width: inherit;
            }
        }
    </style>
    <div class="container-fluid vh-100 p-0 overflow-hidden">
        <div class="row g-0 vh-100">
            <div class="col-lg-5 d-none d-lg-block">
                <div class="side-banner h-100 text-white d-flex flex-column justify-content-between p-5"
                    style="background: linear-gradient(rgba(138, 43, 226, 0.8), rgba(138, 43, 226, 0.8))">

                    <div class="logo">
                        <h4 class="fw-bold">EternalUnion <i class="bi bi-heart-fill text-danger"></i></h4>
                    </div>

                    <div class="content mb-5">
                        <h1 class="display-5 fw-bold mb-4 text-white">رحلتك نحو الشريك المثالي تبدأ هنا</h1>
                        <p class="lead">نحن نؤمن بأن الزواج هو رباط مقدس يستحق العناية والدقة في الاختيار.</p>
                    </div>

                    <div class="stats d-flex gap-4">
                        <div
                            class="stat-card p-3 rounded-4 bg-white bg-opacity-10 backdrop-blur w-50 border border-white border-opacity-25">
                            <h3 class="fw-bold mb-0 text-white">+10k</h3>
                            <small class="text-white-50">عضو موثق</small>
                        </div>
                        <div
                            class="stat-card p-3 rounded-4 bg-white bg-opacity-10 backdrop-blur w-50 border border-white border-opacity-25">
                            <h3 class="fw-bold mb-0 text-white">2.5k</h3>
                            <small class="text-white-50">قصة نجاح</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 bg-white p-5 overflow-auto h-100 text-end" dir="rtl">
                <div class="form-header mb-5">
                    <span class="text-muted small">الخطوة الأساسية</span>
                    <h2 class="fw-bold text-danger mt-2">إنشاء حسابك</h2>
                    <p class="text-muted">أكمل بياناتك الشخصية للبدء في رحلة البحث عن شريك العمر.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


     

                <form action="{{ route('register.store') }}" method="POST">
                    @csrf

                    <h6 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-person-fill text-primary ms-2"></i> المعلومات الشخصية
                    </h6>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الاسم الكامل <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="أدخل اسمك بالكامل" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">الجنس <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select bg-light border-0 p-3 rounded-3" required>
                                <option value="">اختر</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">العمر <span class="text-danger">*</span></label>
                            <input type="number" name="age" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="مثال: 25" required min="18" max="80"
                                value="{{ old('age') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الدولة</label>
                            <input type="text" name="country" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="مثال: مصر">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المدينة</label>
                            <input type="text" name="city" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="مثال: القاهرة">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-stars text-primary ms-2"></i> المظهر وتفاصيل الملف الشخصي
                    </h6>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">الطول (سم)</label>
                            <input type="number" name="height" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="170">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">الوزن (كجم)</label>
                            <input type="number" name="weight" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="70">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">لون البشرة</label>
                            <select name="skin_color" class="form-select bg-light border-0 p-3 rounded-3">
                                <option selected disabled>اختر اللون</option>
                                <option>أبيض</option>
                                <option>قمحي</option>
                                <option>أسمر</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">الحالة الاجتماعية</label>
                            <select name="status" class="form-select bg-light border-0 p-3 rounded-3">
                                <option>أعزب / عزباء</option>
                                <option>مطلق / مطلقة</option>
                                <option>أرمل / أرملة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">المستوى التعليمي</label>
                            <input type="text" name="education"
                                class="form-control bg-light border-0 p-3 rounded-3" placeholder="جامعي">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">المسمى الوظيفي</label>
                            <input type="text" name="job" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="مهندس برمجيات">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-shield-lock-fill text-primary ms-2"></i> معلومات الحساب
                    </h6>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control bg-light border-0 p-3 rounded-3"
                                placeholder="example@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">كلمة المرور</label>
                            <input type="password" name="password"
                                class="form-control bg-light border-0 p-3 rounded-3" placeholder="أدخل كلمة مرور قوية"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 p-3 rounded-pill fw-bold shadow-sm mb-4">إنشاء
                        الحساب ←</button>

                    <div class="text-center mt-3">
                        <p class="text-muted small mb-2">لديك حساب بالفعل؟</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('user.login') }}"
                                class="btn btn-sm btn-outline-danger rounded-pill fw-bold">
                                🔑 تسجيل دخول
                            </a>
                            <a href="{{ route('admin.login') }}"
                                class="btn btn-sm btn-outline-secondary rounded-pill fw-bold">
                                👨‍💼 مسؤول
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
