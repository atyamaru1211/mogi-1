@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/sell.css') }}" />
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
<div class="sell-container">
    <h2 class="sell-title">商品の出品</h2>
    <form class="sell-form" action="">
        <section class="image-upload-section">
            <span class="form-label">商品画像</span>
            <div class="image-upload">
                <label class="image-upload-label" for="image_upload">画像を選択する</label>
                <input class="image-upload-img" type="file" name="image_upload" id="image_upload">
            </div>
        </section>

        <section class="details-section">
            <h3 class="section-title">商品の詳細</h3>
            <div class="form-group">
                <label class="form-label" for="category">カテゴリー</label>
                <ul class="category-list">
                    <!--foreach-->
                    <li>
                        <input class="category-checkbox" type="checkbox" name="category[]" id="category_1" value="1">
                        <label class="category-label" for="category_1">レディース</label>
                    </li>
                </ul>
            </div>
            <div class="form-group select-group">
                <label class="form-label" for="condition">商品の状態</label>
                <select class="form-select" name="condition" id="condition">
                    <option desabled selected>選択してください</option>
                    <!--foreach-->
                </select>
            </div>
        </section>

        <section class="description-section">
            <h3 class="section-title">商品名と説明</h3>
            <div class="form-group">
                <label class="form-label" for="name">商品名</label>
                <input class="form-input" type="text" name="name" id="name">
            </div>
            <div class="form-group">
                <label class="form-label" for="brand">ブランド名</label>
                <input class="form-input" type="text" name="brand" id="brand">
            </div>
            <div class="form-group">
                <label class="form-label" for="description">商品の説明</label>
                <textarea class="form-input description-input" name="description" id="description" row="5"></textarea>
            </div>
            <div class="form-group price-group">
                <label class="form-label" for="price">販売価格</label>
                <input class="form-input price-input" type="number" name="price" id="price">
                <span class="yen">￥</span>
            </div>
        </section>


        <button class="sell-button" type="submit">出品する</button>
    </form>
</div>

<script>
    // JavaScriptで画像プレビュー機能などを実装する場合はここに記述
    const imageUpload = document.getElementById('image_upload');
    const previewContainer = document.querySelector('.preview-container');

    imageUpload.addEventListener('change', function(event) {
        previewContainer.innerHTML = ''; // 既存のプレビューをクリア
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('preview-image');
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    }); 
</script>

@endsection