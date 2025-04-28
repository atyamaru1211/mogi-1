@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/index.css')}}">
@endsection

@section('link')
<div class="header-search">
    <form class="search-form" action="/" method="get">
        @csrf
        <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
    </form>
</div>
<nav class="header-nav">
    <ul class="header-nav__list">
        @if (Auth::check())
            <li class="header-nav__item">
                <form action="/logout" method="post">
                    @csrf
                    <button class="header-nav__link" type="submit">ログアウト</button>
                </form>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__link" href="/mypage">マイページ</a>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__button" href="/sell">出品</a>
            </li>
        @else
            <li class="header-nav__item">
                <a class="header-nav__link" href="/login">ログイン</a>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__link" href="/login">マイページ</a>
            </li>
            <li class="header-nav__item">
                <a class="header-nav__button" href="/login">出品</a>
            </li>
        @endif
    </ul>
</nav>
@endsection

@section('content')
<div class="content">
    <nav class="tab-menu-nav">
        <ul class="tab-menu-nav__list">
            <li class="tab-menu-nav__item">
                <a class="tab-menu-nav__link {{ request()->query('tab') !== 'mylist' ? 'active' : '' }}" href="/{{ request()->query('keyword') ? '?keyword=' . request()->query('keyword') : '' }}">おすすめ</a>
            </li>
            <li class="tab-menu-nav__item">
                <a class="tab-menu-nav__link {{ request()->query('tab') === 'mylist' ? 'active' : '' }}" href="/?tab=mylist{{ request()->query('keyword') ? '&keyword=' . request()->query('keyword') : '' }}">マイリスト</a>
            </li>
        </ul>
    </nav>
    <div class="product-contents">
        @if (request()->query('tab') === 'mylist' && isset($likedItems))
            @foreach ($likedItems as $item)
            <div class="product-content">
                <a class="product-link" href="/item/{{ $item->id }}">
                    <div class="product-img-wrapper">
                        <img class="product-img {{ $item->purchases()->exists() ? 'sold' : '' }}" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @if ($item->purchases()->exists())
                            <span class="sold-label">Sold</span>
                        @endif
                    </div>
                    <div class="product-detail">
                        <p class="product-detail__item">{{ $item->name }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        @else
            @foreach ($items as $item)
            <div class="product-content">
                <a class="product-link" href="/item/{{ $item->id }}">
                    <div class="product-img-wrapper">
                        <img class="product-img {{ $item->purchases()->exists() ? 'sold' : '' }}" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @if ($item->purchases()->exists())
                            <span class="sold-label">Sold</span>
                        @endif
                    </div>
                    <div class="product-detail">
                        <p class="product-detail__item">{{ $item->name }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        @endif
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

                window.location.href = this.getAttribute('href');
            });
        });
    });
</script>

@endsection
