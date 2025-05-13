@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}" />
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
<div class="item-detail">
    <div class="item-detail__left">
        <div class="item-img-wrapper">
            <img class="item-img {{ $item->purchases()->exists() ? 'sold' : '' }}" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
            @if ($item->purchases()->exists())
                <span class="sold-label">Sold</span>
            @endif
        </div>
    </div>
    <div class="item-detail__right">
        <h2 class="item-detail__name">{{ $item->name }}</h2>
        <p class="item-detail__brand">{{ $item->brand }}</p>
        <div class="item-detail__price">
            <span class="price-amount"><span>￥</span><span>{{ number_format($item->price) }}</span></span>
            <span class="price-tax">(税込)</span>
        </div>
        <div class="item-detail__actions">
            @if (Auth::check())
            <button class="like-button {{ $item->isLikedBy(Auth::user()) ? 'liked' : '' }}" id="like-button-{{ $item->id }}">
                <object class="like-button__icon" id="like-icon-{{ $item->id }}" type="image/svg+xml" data="{{ $item->isLikedBy(Auth::user()) ? asset('images/star_after.svg') : asset('images/star.svg') }}" alt="いいね"></object>
                <span class="like-button__count">{{ $item->likes()->count() }}</span>
            </button>
            @else
                <div class="like-button">
                    <a class="like-button__link" href="/login">
                        <object class="like-button__icon" type="image/svg+xml" data="{{ asset('images/star.svg') }}" alt="いいね"></object>
                    </a>
                    <span class="like-button__count">{{ $item->likes()->count() }}</span>
                </div>
            @endif
            <button class="comment-button">
                <img class="comment-button__icon" src="{{ asset('images/comment.svg') }}" alt="コメント">
                <span class="comment-button__count">{{ $item->comments()->count() }}</span>
            </button>
        </div>

        @if (session('success_sold') || $item->purchases()->exists())
        <button class="purchase-button purchase-button--sold" disabled>Sold</button>
        @else
        <a class="purchase-button" href="/purchase/{{ $item->id }}">購入手続きへ</a>
        @endif

        <div class="item-detail__description">
            <h3 class="description-title">商品説明</h3>
            <p class="description-text">{{ $item->description }}</p>
        </div>

        <div class="item-detail__attributes">
            <h3 class="attribute-title">商品の情報</h3>
            <div class="attribute__list">
                <div class="attribute__item">
                    <span class="attribute__label">カテゴリー</span>
                    <div class="attribute__categories">
                        @foreach ($item->categories as $category)
                            <span class="attribute__category">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="attribute__item">
                    <span class="attribute__label">商品の状態</span>
                    <span class="attribute__condition">
                        @if ($item->condition == 1)
                            良好
                        @elseif ($item->condition == 2)
                            目立った傷や汚れなし
                        @elseif ($item->condition == 3)
                            やや傷や汚れあり
                        @elseif ($item->condition == 4)
                            状態が悪い
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="item-detail__comments">
            <h3 class="comment-title">コメント<span class="comment-count">({{ $item->comments()->count() }})</span></h3>
            <ul class="comment-list">
                @foreach ($item->comments as $comment)
                    <li class="comment-item">
                        <p class="comment-item__user">
                            <span class="comment-item__user-icon"></span>
                            <span class="comment-item__user-name">{{ $comment->user->name }}</span>
                        </p>
                        <p class="comment-item__text">{{ $comment->content }}</p>
                    </li>
                @endforeach
            </ul>
            <form class="comment-form__form" action="/item/{{ $item->id }}/comment" method="POST">
                @csrf
                <h4 class="comment-form__title">商品へのコメント</h4>
                <textarea class="comment-form__textarea" name="content"></textarea>
                <p class="error-message">
                    @error('content')
                    {{ $message }}
                    @enderror
                </p>
                @if (Auth::check())
                    <button class="comment-form__button" type="submit">コメントを送信する</button>
                @else
                    <a class="comment-form__button" href="/login">コメントを送信する</a>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButton = document.getElementById('like-button-{{ $item->id }}');
    const likeIconObject = document.getElementById('like-icon-{{ $item->id }}');

    if (likeButton && likeIconObject) {
        likeButton.addEventListener('click', function(event) {
            event.preventDefault();

            const itemId = '{{ $item->id }}';
            const url = `/item/${itemId}/like`;
            const likeCountSpan = likeButton.querySelector('.like-button__count');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (likeCountSpan) {
                    likeCountSpan.textContent = data.like_count;
                }

                const isLiked = data.liked;
                const defaultIconPath = '{{ asset('images/star.svg') }}';
                const likedIconPath = '{{ asset('images/star_after.svg') }}';

                // アイコンの切り替え
                likeIconObject.setAttribute('data', isLiked ? likedIconPath : defaultIconPath);

                // 'liked' クラスの付与・削除 (CSS でスタイルを調整する場合)
                if (isLiked) {
                    likeButton.classList.add('liked');
                } else {
                    likeButton.classList.remove('liked');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>

@endsection
