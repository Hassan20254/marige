@extends('layouts.app')

@section('content')
    <div class="container py-5" dir="rtl">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-danger">لوحة تحكم المشرف</h2>
                    <p class="text-muted mb-0">من هنا يمكنك تفعيل اشتراك المستخدمين للسماح لهم بالتواصل.</p>
                </div>
                {{-- <a href="{{ route('home') }}" class="btn btn-outline-secondary">العودة للرئيسية</a> --}}
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive bg-dark text-white rounded-4 p-3 shadow-sm">
            <table class="table table-dark table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الجنس</th>
                        <th>العمر</th>
                        <th>المدينة</th>
                        <th>الحالة الحالية</th>
                        <th>الصلاحيات</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->age }}</td>
                            <td>{{ $user->city }}</td>
                            <td>
                                @if ($user->is_admin)
                                    <span class="badge bg-warning text-dark">أدمن</span>
                                @elseif($user->is_subscribed)
                                    <span class="badge bg-success">مفعل</span>
                                @else
                                    <span class="badge bg-secondary">غير مفعل</span>
                                @endif
                            </td>
                            <td>{{ $user->is_subscribed ? 'يستطيع التواصل' : 'لا يستطيع التواصل' }}</td>
                            <td>
                                <form action="{{ route('admin.user.toggleSubscription', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $user->is_subscribed ? 'btn-danger' : 'btn-success' }}">
                                        {{ $user->is_subscribed ? 'إيقاف' : 'تفعيل' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
