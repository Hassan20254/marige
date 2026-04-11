@extends('layouts.app')

@section('content')

    <style>
        body {
            background: linear-gradient(135deg, #fff5f7, #ffe4ec);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .registration-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .registration-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(216, 27, 96, 0.15);
        }

        .registration-title {
            color: #D81B60;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .google-info {
            background: #fce4ec;
            border-left: 4px solid #D81B60;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .google-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #eee;
            transition: .3s;
            margin-bottom: 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #D81B60;
            box-shadow: 0 0 0 3px rgba(216, 27, 96, 0.15);
        }

        .register-btn {
            background: #D81B60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px;
            transition: .3s;
            width: 100%;
            border: none;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(216, 27, 96, 0.25);
            background: #ad144a;
        }

        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 5px;
        }

        .info-message {
            color: #1976d2;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #e3f2fd;
            border-radius: 5px;
        }

        .required-field {
            color: #D81B60;
            font-weight: bold;
        }
    </style>

    <div class="registration-wrapper" dir="rtl">
        <div class="registration-card">

            <h3 class="registration-title">🎉 أكمل بيانات حسابك من Google</h3>

            @if ($errors->any())
                <div class="error-message">
                    <strong>خطأ:</strong>
                    <ul class="mb-0" style="margin-top: 5px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('info'))
                <div class="info-message">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            <div class="google-info">
                <p><strong>📧 البريد الإلكتروني:</strong> {{ $email }}</p>
                <p><strong>👤 الاسم:</strong> {{ $name }}</p>
                <small style="color: #666;">البيانات مُسحوبة من حسابك على Google</small>
            </div>

            <form action="{{ route('complete.google.registration.store') }}" method="POST">
                @csrf

                <div>
                    <label for="gender"><span class="required-field">*</span> الجنس</label>
                    <select name="gender" id="gender" class="form-select" required>
                        <option value="">اختر الجنس</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>♂️ ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>♀️ أنثى</option>
                    </select>
                </div>

                <div>
                    <label for="age"><span class="required-field">*</span> العمر</label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="أدخل عمرك"
                        required min="18" max="80" value="{{ old('age') }}">
                </div>

                <div>
                    <label for="country"><span class="required-field">*</span> الدولة</label>
                    <input type="text" name="country" id="country" class="form-control" placeholder="مثال: مصر"
                        required value="{{ old('country') }}">
                </div>

                <div>
                    <label for="city"><span class="required-field">*</span> المدينة</label>
                    <input type="text" name="city" id="city" class="form-control" placeholder="مثال: القاهرة"
                        required value="{{ old('city') }}">
                </div>

                <div>
                    <label for="height">الطول (اختياري)</label>
                    <input type="number" name="height" id="height" class="form-control" placeholder="مثال: 175"
                        min="100" max="220" value="{{ old('height') }}">
                </div>

                <div>
                    <label for="weight">الوزن (اختياري)</label>
                    <input type="number" name="weight" id="weight" class="form-control" placeholder="مثال: 75"
                        min="30" max="200" value="{{ old('weight') }}">
                </div>

                <div>
                    <label for="skin_color">لون البشرة (اختياري)</label>
                    <input type="text" name="skin_color" id="skin_color" class="form-control" placeholder="مثال: قمحي"
                        value="{{ old('skin_color') }}">
                </div>

                <div>
                    <label for="status">الحالة الاجتماعية (اختياري)</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">لم أختر</option>
                        <option value="أعزب" {{ old('status') == 'أعزب' ? 'selected' : '' }}>أعزب</option>
                        <option value="مطلق" {{ old('status') == 'مطلق' ? 'selected' : '' }}>مطلق</option>
                        <option value="أرمل" {{ old('status') == 'أرمل' ? 'selected' : '' }}>أرمل</option>
                    </select>
                </div>

                <div>
                    <label for="education">التعليم (اختياري)</label>
                    <input type="text" name="education" id="education" class="form-control" placeholder="مثال: جامعي"
                        value="{{ old('education') }}">
                </div>

                <div>
                    <label for="job">الوظيفة (اختياري)</label>
                    <input type="text" name="job" id="job" class="form-control"
                        placeholder="مثال: مهندس برمجيات" value="{{ old('job') }}">
                </div>

                <button type="submit" class="register-btn">
                    ✅ إنشاء الحساب والدخول
                </button>
            </form>

        </div>
    </div>

@endsection
