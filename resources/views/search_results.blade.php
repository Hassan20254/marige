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
    <div class="container py-5" dir="rtl">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8 text-start text-md-end">
                <h2 class="fw-bold text-danger">نتائج البحث المقترحة</h2>
                <p class="text-muted text-center text-md-end">بناءً على بياناتك، إليك الأشخاص الأكثر توافقاً معك</p>
            </div>
            <div class="col-md-4 text-center text-md-start mt-3 mt-md-0">
                <a href="{{ route('chat.inbox') }}" class="btn btn-danger rounded-pill px-4 me-2">صندوق الوارد</a>
                <a href="{{ route('logout') }}" class="btn btn-outline-danger rounded-pill px-4">خروج</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <hr class="border-2 border-danger">
            </div>
        </div>

        <div class="row g-4">
            @forelse($results as $item)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden person-card">
                        <div class="card-img-top bg-gradient p-4 text-center text-white"
                            style="background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
                            <div class="avatar-placeholder mx-auto mb-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-white"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill text-secondary fs-1"></i>
                            </div>
                            <h5 class="mb-0 text-dark fw-bold">{{ $item->name }}</h5>
                            <span class="badge bg-danger rounded-pill px-3 mt-2">{{ $item->city }}</span>
                        </div>

                        <div class="card-body text-end p-4">
                            <div class="info-grid mb-3">
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                    <span class="text-muted small">العمر:</span>
                                    <span class="fw-bold">{{ $item->age }} سنة</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                    <span class="text-muted small">الطول/الوزن:</span>
                                    <span class="fw-bold">{{ $item->height }} سم / {{ $item->weight }} كجم</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                    <span class="text-muted small">الحالة الاجتماعية:</span>
                                    <span class="fw-bold">{{ $item->status }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                    <span class="text-muted small">الوظيفة:</span>
                                    <span class="fw-bold text-truncate"
                                        style="max-width: 150px;">{{ $item->job }}</span>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <a href="/chat/{{ $item->id }}"
                                    class="btn btn-danger rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="bi bi-chat-heart-fill ms-2"></i> تواصل الآن
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-search fs-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">عذراً، لم نجد نتائج تطابق بحثك حالياً</h4>
                    <a href="/" class="btn btn-outline-danger mt-3 rounded-pill px-4">العودة للرئيسية</a>
                </div>
            @endforelse
        </div>
    </div>


    <style>
        body {
            background-color: #fbfbfb;
            font-family: 'Tajawal', sans-serif;
        }

        .person-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .person-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-danger {
            background-color: #D81B60 !important;
        }

        .btn-danger {
            background-color: #D81B60;
            border: none;
        }

        .btn-danger:hover {
            background-color: #ad144a;
        }
    </style>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
