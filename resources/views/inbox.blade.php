@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="direction: rtl; text-align: right;">
        <h2 class="mb-4" style="color: #00FFE6; font-weight: bold;">صندوق الوارد</h2>

        <div class="list-group">
            @forelse($conversations as $conv)
                @php
                    $otherUser = $conv->sender_id == $myId ? $conv->receiver : $conv->sender;
                    $unreadCount = \App\Models\Message::where('sender_id', $otherUser->id)
                        ->where('receiver_id', $myId)
                        ->where('is_read', false)
                        ->count();
                @endphp

                <a href="{{ route('chat.index', $otherUser->id) }}"
                    class="list-group-item list-group-item-action bg-dark text-white border-secondary mb-3 rounded-4 d-flex justify-content-between align-items-center p-3 shadow">

                    <div class="d-flex align-items-center">
                        <div class="ms-3">
                            <h5 class="mb-1" style="color: #00FFE6;">{{ $otherUser->name }}</h5>
                            <p class="mb-0 text-secondary">
                                @if ($conv->sender_id == $myId)
                                    <span class="text-info">أنت:</span>
                                @endif
                                {{ Str::limit($conv->body, 50) }}
                            </p>
                        </div>
                    </div>

                    <div class="text-start">
                        <small class="text-muted d-block mb-2">{{ $conv->created_at->diffForHumans() }}</small>
                        @if ($unreadCount > 0)
                            <span class="badge rounded-pill bg-danger p-2 px-3 shadow-sm">{{ $unreadCount }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-center mt-5 p-5 border border-secondary rounded-5">
                    <h4 class="text-secondary">لا توجد محادثات سابقة.</h4>
                    <a href="/" class="btn mt-3" style="background-color: #00FFE6; color: black;">ابدأ البحث عن
                        شريكك</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
