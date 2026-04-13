@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary: #e91e63;   /* وردي ناعم */
        --gold: #d4af37;      /* ذهبي */
        --bg: #eeb4c77e;
        --card: #17171c;
        --text: #f5f5f5;
        --muted: #b0b0b0;
    }

    body {
        background: var(--bg);
        font-family: "Segoe UI", sans-serif;
    }

    /* HEADER */
    .inbox-header {
        background: linear-gradient(135deg, var(--primary), #ad1457);
        padding: 18px 20px;
        border-radius: 18px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        box-shadow: 0 15px 35px rgba(233, 30, 99, 0.25);
    }

    .inbox-title {
        font-weight: bold;
        font-size: 18px;
    }

    .search-btn {
        background: white;
        color: var(--primary);
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: bold;
        transition: 0.3s;
    }

    .search-btn:hover {
        transform: scale(1.05);
    }

    /* CARD */
    .chat-card {
        background: var(--card);
        border-radius: 18px;
        padding: 15px;
        margin-bottom: 12px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: 0.3s;
        text-decoration: none;
        color: var(--text);
        display: block;
        position: relative;
        overflow: hidden;
    }

    .chat-card:hover {
        transform: translateY(-3px);
        border-color: var(--primary);
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15);
    }

    .name {
        color: var(--primary);
        font-weight: bold;
        font-size: 16px;
    }

    .last-msg {
        color: var(--muted);
        font-size: 13px;
        margin-top: 5px;
    }

    .time {
        font-size: 12px;
        color: #888;
    }

    .badge-unread {
        background: var(--primary);
        color: white;
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 12px;
        box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3);
    }

    /* GOLD LINE */
    .chat-card::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(var(--gold), var(--primary));
    }
</style>

<div class="container mt-4" style="direction: rtl; text-align: right;">

    {{-- HEADER --}}
    <div class="inbox-header">

        <div class="inbox-title">💍 الرسائل الخاصة</div>

        <a href="{{ route('search.index') }}" class="search-btn">
            🔍 بحث عن شريك
        </a>

    </div>

    {{-- CHATS --}}
    @forelse($conversations as $conv)

        @php
            $otherUser = $conv->sender_id == $myId ? $conv->receiver : $conv->sender;

            $unreadCount = \App\Models\Message::where('sender_id', $otherUser->id)
                ->where('receiver_id', $myId)
                ->where('is_read', false)
                ->count();
        @endphp

        <a href="{{ route('chat.index', $otherUser->id) }}" class="chat-card">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <div class="name">
                        💕 {{ $otherUser->name }}
                    </div>

                    <div class="last-msg">
                        @if ($conv->sender_id == $myId)
                            <span style="color:var(--gold);">أنت:</span>
                        @endif

                        {{ Str::limit($conv->body, 60) }}
                    </div>
                </div>

                <div class="text-start">

                    <div class="time mb-2">
                        {{ $conv->created_at->diffForHumans() }}
                    </div>

                    @if ($unreadCount > 0)
                        <span class="badge-unread">
                            {{ $unreadCount }}
                        </span>
                    @endif

                </div>

            </div>

        </a>

    @empty

        <div class="text-center mt-5 text-white">
            <h4 style="color:#aaa;">لا توجد رسائل حالياً</h4>

            <a href="{{ route('search.index') }}" class="btn mt-3"
               style="background:var(--primary); color:white; border-radius:12px;">
                💍 ابحث عن شريك مناسب
            </a>
        </div>

    @endforelse

</div>

@endsection