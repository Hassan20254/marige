<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج البحث</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #d81b60;
            --primary-dark: #ad1457;
            --soft-bg: #fdf7f9;
            --card-bg: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
        }

        body {
            background: linear-gradient(180deg, #fff 0%, var(--soft-bg) 100%);
            font-family: 'Tajawal', sans-serif;
            color: var(--text);
        }

        /* HEADER */
        .page-title {
            font-weight: 800;
            color: var(--primary);
        }

        /* CARD */
        .person-card {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            background: var(--card-bg);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            transition: 0.3s ease;
            position: relative;
        }

        .person-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.12);
        }

        /* TOP AREA */
        .card-header-custom {
            background: linear-gradient(135deg, #fff0f5, #ffe4ec);
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: bold;
            margin: auto;
            box-shadow: 0 10px 25px rgba(216,27,96,0.25);
        }

        /* STATUS */
        .status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .online {
            background: #e8fff1;
            color: #16a34a;
        }

        .offline {
            background: #f3f4f6;
            color: #6b7280;
        }

        .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* INFO */
        .info {
            padding: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
        }

        .info-row span:first-child {
            color: var(--muted);
        }

        .info-row span:last-child {
            font-weight: 600;
        }

        /* BUTTON */
        .btn-love {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: white;
            padding: 12px;
            border-radius: 999px;
            font-weight: 700;
            transition: 0.3s;
            width: 100%;
        }

        .btn-love:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px rgba(216,27,96,0.3);
        }

        /* TOP BAR */
        .top-bar {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

    </style>
</head>

<body>

<div class="container py-5">

    <!-- HEADER -->
    <div class="top-bar mb-4 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="page-title mb-1">نتائج البحث</h2>
            <small class="text-muted">أشخاص مقترحين بناءً على التوافق</small>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('chat.inbox') }}" class="btn btn-danger rounded-pill px-4">صندوق الوارد</a>
            <a href="{{ route('logout') }}" class="btn btn-outline-danger rounded-pill px-4">خروج</a>
        </div>
    </div>

    <!-- CARDS -->
    <div class="row g-4">

        @forelse($results as $item)

        <div class="col-lg-4 col-md-6">

            <div class="person-card">

                <!-- HEADER -->
                <div class="card-header-custom">

                    <div class="avatar mb-3">
                        {{ substr($item->name, 0, 1) }}
                    </div>

                    <h5 class="fw-bold mb-1 d-flex justify-content-center align-items-center gap-2">
                        {{ $item->name }}

                        <span class="status {{ $item->is_online ? 'online' : 'offline' }}">
                            <span class="dot"></span>
                            {{ $item->is_online ? 'متصل' : 'غير متصل' }}
                        </span>
                    </h5>

                    <small class="text-muted d-block">
                        {{ $item->is_online ? '' : $item->last_seen_text }}
                    </small>

                    <span class="badge bg-light text-dark mt-2 px-3 py-2 rounded-pill">
                        {{ $item->city }}
                    </span>

                </div>

                <!-- BODY -->
                <div class="info">

                    <div class="info-row">
                        <span>العمر</span>
                        <span>{{ $item->age }} سنة</span>
                    </div>

                    <div class="info-row">
                        <span>الطول / الوزن</span>
                        <span>{{ $item->height }} سم / {{ $item->weight }} كجم</span>
                    </div>

                    <div class="info-row">
                        <span>الحالة</span>
                        <span>{{ $item->status }}</span>
                    </div>

                    <div class="info-row">
                        <span>الوظيفة</span>
                        <span>{{ $item->job }}</span>
                    </div>

                    <div class="mt-3">
                        <a href="/chat/{{ $item->id }}" class="btn-love">
                            💌 تواصل الآن
                        </a>
                    </div>

                </div>

            </div>

        </div>

        @empty

        <div class="col-12 text-center py-5">
            <h4 class="text-muted">لا توجد نتائج</h4>
        </div>

        @endforelse

    </div>

</div>

</body>
</html>