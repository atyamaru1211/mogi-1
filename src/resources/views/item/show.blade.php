@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}" />
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
<div class="item-detail">
    <div class="item-detail__left">
        <div class="item-img">
            <img src="item-img__content" alt="商品画像">
        </div>
    </div>
    <div class="item-detail__right">
        <h2 class="item-detail__name">商品名がここに入る</h2>
        <p class="item-detail__brand">ブランド名</p>
        <div class="item-detail__price">
            <span class="price-amount">￥47,000</span>
            <span class="price-tax">(税込)</span>
        </div>
        <div class="item-detail__actions">
            <button class="like-button">
                <img class="like-button__icon" src="{{ asset('images/star.png') }}" alt="いいね">
                <span class="like-button__count">3</span>
            </button>
            <button class="comment-button">
                <img class="comment-button__icon" src="{{ asset('images/comment.png') }}" alt="コメント">
                <span class="comment-button__count">1</span>
            </button>
        </div>
        <button class="purchase-button">購入手続きへ</button>

        <div class="item-detail__description">
            <h3 class="description-title">商品説明</h3>
            <p class="description-text">
                カラー：グレー
            </p>
        </div>

        <div class="item-detail__attributes">
            <h3 class="attribute-title">商品の情報</h3>
            <div class="attribute__list">
                <div class="attribute__item">
                    <span class="attribute__label">カテゴリー</span>
                    <div class="attribute__categories">
                        <!--繰り返しのあれ入れる？-->
                        <span class="attribute__category">洋服</span>
                        <span class="attribute__category">メンズ</span>
                    </div>
                </div>
                <div class="attribute__item">
                    <span class="attribute__label">商品の状態</span>
                    <span class="attribute__condition">良好</span>
                </div>
            </div>
        </div>

        <div class="item-detail__comments">
            <h3 class="comment-title">コメント<span class="comment-count">(1)</span></h3>
            <ul class="comment-list">
                <li class="comment-item">
                    <p class="comment-item__user">
                        <span class="comment-item__user-icon"></span>
                        <span class="comment-item__user-name">admin</span>
                    </p>
                    <p class="comment-item__text">こちらにコメントが入ります。</p>
                </li>
            </ul>
            <form class="comment-form__form">
                <h4 class="comment-form__title">商品へのコメント</h4>
                <textarea class="comment-form__textarea"></textarea>
                <button class="comment-form__button" type="submit">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection
