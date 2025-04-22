@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/purchase.css')}}">
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
<form action="/purchase/{{ $item->id }}" method="POST">
    @csrf
    <div class="purchase-container">
        <div class="purchase-left">
            <section class="item-section">
                <div class="item-image">
                    <img class="item-image-link" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-info">
                    <h2 class="item-info__name">{{ $item->name }}</h2>
                    <p class="item-info__price">
                        <span>￥</span>
                        <span>{{ number_format($item->price) }}</span>
                    </p>
                </div>
            </section>

            <section class="payment-section">
                <h3 class="payment-section__title">支払い方法</h3>
                <div class="payment__select-inner">
                    <select class="payment__select" name="payment_method" id="payment_method">
                        <option data-display="選択してください" value="">選択してください</option>
                        <option data-display="コンビニ払い" value="1" {{ old('payment_method') == '1' ? 'selected' : '' }}>コンビニ払い</option>
                        <option data-display="カード払い" value="2" {{ old('payment_method') == '2' ? 'selected' : '' }}>カード払い</option>
                    </select>
                </div>
                <p class="error-message">
                    @error('payment_method')
                    {{ $message }}
                    @enderror
                </p>
            </section>

            <section class="shipping-section">
                <h3 class="shipping-section__title">配送先</h3>
                <div class="shipping-address">
                    <p class="shipping-address__postal-code">
                        <span>〒</span><span>{{ $shippingAddress['postal_code'] ?? $profile->postal_code ?? '' }}</span>
                    </p>
                    <p class="shipping-address__text">
                        <span>
                            {{ $shippingAddress['address'] ?? $profile->address ?? '' }}
                        </span>
                        <span>
                            {{ $shippingAddress['building'] ?? $profile->building ?? '' }}
                        </span>
                    </p>
                    <div class="shipping-address__change">
                        <a class="shipping-address__change-link" href="/purchase/address/{{ $item->id }}">変更する</a>
                    </div>
                    <p class="error-message">
                        @error('address_id')
                        {{ $message }}
                        @enderror
                    </p>

                </div>
            </section>
        </div>

        <div class="purchase-right">
            <section class="summary-section">
                <div class="summary-details">
                    <p class="summary-details__price">
                        <span class="price__label">商品代金</span>
                        <span class="price__value">￥ {{ number_format($item->price) }}</span>
                    </p>
                    <div class="divider"></div>
                    <p class="summary-details__payment">
                        <span class="payment__lavel">支払い方法</span>
                        <span class="payment__value"></span>
                    </p>
                </div>
            </section>
            <button class="purchase-button" type="submit">購入する</button>
        </div>
    </div>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.querySelector('.payment__select');
        const paymentOptions = paymentSelect.querySelectorAll('option');
        const paymentValueSpan = document.querySelector('.summary-details__payment .payment__value');

        const oldPaymentMethod = "{{ old('payment_method') }}";

        if (oldPaymentMethod) {
            paymentSelect.value = oldPaymentMethod;
        }

        paymentValueSpan.textContent = paymentSelect.options[paymentSelect.selectedIndex].textContent;
        paymentSelect.addEventListener('change', function() {
            paymentOptions.forEach(option => {
                option.textContent = option.textContent.replace('✓ ', '');
            });
            const selectedOption = this.options[this.selectedIndex];
            selectedOption.textContent = '✓ ' + selectedOption.textContent;
            paymentValueSpan.textContent = selectedOption.textContent.replace('✓ ', '');
        });

        const initialValue = paymentSelect.value;
        paymentOptions.forEach(option => {
            if (option.value === initialValue && initialValue !== '') {
                option.textContent = '✓ ' + option.textContent;
            }
        });
    });
</script>

@endsection