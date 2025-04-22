@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css')}}">
@endsection

@section('link')
<div class="header-search">
    <form class="search-form" action="/" method="get">
        @csrf
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request()->query('keyword') }}">
    </form>
</div>
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item">
            <a class="header-nav__link" href="/logout">ログアウト</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__link" href="/mypage">マイページ</a>
        </li>
        <li class="header-nav__item">
            <a class="header-nav__button" href="/sell">出品</a>
        </li>
    </ul>
</nav>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image">
            <span class="profile-image-icon"></span>
        </div>
        <h2 class="profile-username">ユーザー名</h2>
        <a class="profile-edit-button" href="/mypage/profile">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <nav class="tab-menu-nav">
            <ul class="tab-menu-nav__list">
                <li class="tab-menu-nav__item">
                    <a class="tab-menu-nav__link {{ request()->query('tab') === 'sell' ? 'active' : '' }}" href="/mypage?tab=sell">出品した商品</a>
                </li>
                <li class="tab-menu-nav__item">
                    <a class="tab-menu-nav__link {{ request()->query('tab') === 'buy' ? 'active' : '' }}" href="/mypage?tab=buy">購入した商品</a>
                </li>
            </ul>
        </nav>
        <div class="product-contents">
            <div class="product-content">
                <a class="product-link" href=""></a>
                <img class="product-img" src="" alt="商品画像">
                <div class="product-detail">
                    <p class="product-detail__item">商品名</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-menu-nav__link');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); 

                tabLinks.forEach(tab => {
                    tab.classList.remove('active');
                });

                this.classList.add('active');

                // ここで対応するコンテンツの表示/非表示を制御する処理を追加できます
            });
        });
    });
</script>
@endsection