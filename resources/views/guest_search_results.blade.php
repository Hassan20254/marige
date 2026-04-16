@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="direction: rtl; text-align: right;">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-danger">نتائج البحث</h2>
                <p class="text-muted">بناءً على بحثك، إليك النتائج المتاحة</p>
            </div>
            <div class="col-md-4 text-start">
                <a href="{{ route('guest.search') }}" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="fas fa-search me-2"></i> بحث جديد
                </a>
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
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <span class="badge bg-danger rounded-pill px-3 me-2">{{ $item->city }}</span>
                                <span class="badge {{ $item->is_online ? 'bg-success' : 'bg-secondary' }} rounded-pill px-2">
                                    <i class="bi bi-circle-fill fa-xs me-1"></i>
                                    {{ $item->is_online ? 'متصل الآن' : 'غير متصل' }}
                                </span>
                            </div>
                            @if (!$item->is_online)
                                <small class="text-muted d-block mt-1">{{ $item->last_seen_text }}</small>
                            @endif
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
                                <a href="{{ route('register') }}?target_user_id={{ $item->id }}" class="btn btn-danger rounded-pill py-2 fw-bold shadow-sm">
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
                    <a href="{{ route('guest.search') }}" class="btn btn-outline-danger mt-3 rounded-pill px-4">بحث جديد</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">سجل حسابك لتتواصل مع <span id="modalUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">يجب إنشاء حساب أولاً لتتمكن من التواصل مع الأعضاء.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}?target_user_id=" class="btn btn-primary w-100" id="registerLink">
                            <i class="fas fa-user-plus me-2"></i> create a new account
                        </a>
                        <form action="{{ route('user.login.submit') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="target_user_id" id="modalTargetUserIdLogin">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i> login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
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

    <script>
        function showRegisterModal(userId, userName) {
            // Set the target user ID in the login form
            document.getElementById('modalTargetUserIdLogin').value = userId;
            
            // Set the user name in the modal title
            document.getElementById('modalUserName').textContent = userName;
            
            // Update the registration link with the target user ID
            const registerLink = document.getElementById('registerLink');
            if (registerLink) {
                registerLink.href = "{{ route('home') }}?target_user_id=" + userId;
            }
            
            // Show the modal
            var modal = new bootstrap.Modal(document.getElementById('registerModal'));
            modal.show();
        }
    </script>
@endsection
