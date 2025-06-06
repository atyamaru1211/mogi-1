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
    </ul>
</nav>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image" style="@if($user->profile && $user->profile->profile_image_path) background-image: url('{{ asset($user->profile->profile_image_path) }}'); background-size: cover; background-position: center; @endif">
            @if(!$user->profile || !$user->profile->profile_image_path)    
                <span class="profile-image-icon"></span>
            @endif
        </div>
        <h2 class="profile-username">{{ $user->profile ? $user->profile->name : $user->name }}</h2>
        <a class="profile-edit-button" href="/mypage/profile">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <nav class="tab-menu-nav">
            <ul class="tab-menu-nav__list">
                <li class="tab-menu-nav__item">
                    <a class="tab-menu-nav__link {{ request()->query('tab') === 'sell' || request()->query('tab') === null ?  'active' : '' }}" href="/mypage?tab=sell">出品した商品</a>
                </li>
                <li class="tab-menu-nav__item">
                    <a class="tab-menu-nav__link {{ request()->query('tab') === 'buy' ? 'active' : '' }}" href="/mypage?tab=buy">購入した商品</a>
                </li>
            </ul>
        </nav>
        <div class="product-contents">
            @if (request()->query('tab') === 'sell' || request()->query('tab') === null)
                @forelse ($soldItems as $item)
                    <div class="product-content">
                        <a class="product-link" href="/item/{{ $item->id }}"></a>
                        <img class="product-img" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        <div class="product-detail">
                            <p class="product-detail__item">{{ $item->name }}</p>
                        </div>
                    </div>
                @empty
                @endforelse
            @elseif (request()->query('tab') === 'buy')
                @forelse ($boughtItems as $purchase)
                    <div class="product-content">
                        <a class="product-link" href="/item/{{ $purchase->item->id }}"></a>
                        <img class="product-img" src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}">
                        <div class="product-detail">
                            <p class="product-detail__item">{{ $purchase->item->name }}</p>
                        </div>
                    </div>
                @empty
                @endforelse
            @endif
        </div>
    </div>
</div>

@endsection