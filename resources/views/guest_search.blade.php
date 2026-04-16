@extends('layouts.app')

@section('content')

<div class="search-page py-5" style="direction: rtl; text-align: right;">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-7">

                <!-- CARD -->
                <div class="search-card p-5">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold">ابحث عن شريك حياتك</h2>
                        <p class="text-muted">اختر نوع الشريك الذي تبحث عنه للبدء</p>
                    </div>

                    <form action="{{ route('guest.search.results') }}" method="GET">

                        @csrf

                        <!-- GENDER SELECTION -->
                        <div class="gender-box">

                            <label class="section-title">أنا أبحث عن:</label>

                            <div class="gender-options mt-3">

                                <label class="gender-card">
                                    <input type="radio" name="gender" value="male" required>
                                    <div class="gender-content">
                                        <div class="icon">👨</div>
                                        <div>رجل</div>
                                    </div>
                                </label>

                                <label class="gender-card">
                                    <input type="radio" name="gender" value="female" required>
                                    <div class="gender-content">
                                        <div class="icon">👩</div>
                                        <div>امرأة</div>
                                    </div>
                                </label>

                            </div>

                        </div>

                        <!-- BUTTON -->
                        <div class="text-center mt-5">

                            <button type="submit" class="btn-search">
                                🔍 ابدأ البحث الآن
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<style>

/* PAGE BACKGROUND */
.search-page{
    background: linear-gradient(180deg, #fff 0%, #fff5f7 100%);
}

/* MAIN CARD */
.search-card{
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    border: 1px solid #f3f4f6;
}

/* TITLE */
.section-title{
    font-weight: 700;
    color: #111827;
}

/* GENDER LAYOUT */
.gender-options{
    display: flex;
    gap: 15px;
    justify-content: center;
}

/* HIDE RADIO */
.gender-card input{
    display: none;
}

/* CARD STYLE */
.gender-card{
    flex: 1;
    cursor: pointer;
}

.gender-content{
    border: 2px solid #eee;
    border-radius: 18px;
    padding: 25px;
    text-align: center;
    transition: 0.25s;
    background: #fff;
}

.gender-content .icon{
    font-size: 30px;
    margin-bottom: 8px;
}

/* HOVER */
.gender-card:hover .gender-content{
    border-color: #be123c;
    transform: translateY(-3px);
}

/* SELECTED */
.gender-card input:checked + .gender-content{
    border-color: #be123c;
    background: rgba(190, 18, 60, 0.05);
    box-shadow: 0 10px 30px rgba(190, 18, 60, 0.12);
}

/* BUTTON */
.btn-search{
    background: linear-gradient(135deg, #be123c, #e11d48);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 999px;
    font-weight: 700;
    transition: 0.3s;
    box-shadow: 0 15px 40px rgba(225, 29, 72, 0.2);
}

.btn-search:hover{
    transform: translateY(-3px);
}

</style>

@endsection