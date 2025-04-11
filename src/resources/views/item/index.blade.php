@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/index.css')}}">
@endsection

@section('link')
<div class="header-search">
    <form class="search-form">
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？">
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
<div class="tab-menu">
    <nav class="tab-menu-nav">
        <ul class="tab-menu-nav__list">
            <li class="tab-menu-nav__item">
                <a class="tab-menu-nav__link" href="#">おすすめ</a>
            </li>
            <li class="tab-menu-nav__item">
                <a class="tab-menu-nav__link" href="/?tab=mylist">マイリスト</a>
            </li>
        </ul>
    </nav>
    <div class="product-contents">
        <!--foreach-->
        <div class="product-content">
            <a class="product-link" href="/item/:item_id"></a>
            <img class="product-img" src="" alt="商品画像">
            <div class="product-detail">
                <p class="product-detail__item">商品名</p>
            </div>
        </div>
        <!--endforeach-->
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
