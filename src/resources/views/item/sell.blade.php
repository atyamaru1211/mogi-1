@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/sell.css') }}" />
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
<div class="sell-container">
    <h2 class="sell-title">商品の出品</h2>
    <form class="sell-form" action="/sell" method="POST" enctype="multipart/form-data">
        @csrf
        <section class="image-upload-section">
            <span class="form-label">商品画像</span>
            <div class="image-upload">
                <label class="image-upload-label" for="image_upload">画像を選択する</label>
                <input class="image-upload-img" type="file" name="image_upload" id="image_upload">
                <p class="error-message">
                    @error('image_upload')
                        {{ $message }}
                    @enderror
                </p>
                <div class="preview-container">
                <output id="image_preview_list" class="image_output"></output>
                </div>
            </div>
        </section>

        <section class="details-section">
            <h3 class="section-title">商品の詳細</h3>
            <div class="form-group">
                <label class="form-label" for="category">カテゴリー</label>
                <ul class="category-list">
                    @foreach ($categories as $category)
                    <li>
                        <input class="category-checkbox" type="checkbox" name="category[]" id="category_{{ $category->id }}" value="{{ $category->id }}"{{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
                        <label class="category-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
                    </li>
                    @endforeach
                </ul>
                <p class="error-message">
                    @error('category')
                        {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="form-group select-group">
                <label class="form-label" for="condition">商品の状態</label>
                <select class="form-select" name="condition" id="condition">
                    <option value="" desabled selected>選択してください</option>
                    <option value="1" {{ old('condition') == '1' ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == '2' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == '3' ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="4" {{ old('condition') == '4' ? 'selected' : '' }}>状態が悪い</option>
                </select>
                <p class="error-message">
                    @error('condition')
                        {{ $message }}
                    @enderror
                </p>
            </div>
        </section>

        <section class="description-section">
            <h3 class="section-title">商品名と説明</h3>
            <div class="form-group">
                <label class="form-label" for="name">商品名</label>
                <input class="form-input" type="text" name="name" id="name" value="{{ old('name') }}">
                <p class="error-message">
                    @error('name')
                        {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="form-group">
                <label class="form-label" for="brand">ブランド名</label>
                <input class="form-input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="description">商品の説明</label>
                <textarea class="form-input description-input" name="description" id="description" row="5">{{ old('description') }}</textarea>
                <p class="error-message">
                    @error('description')
                        {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="form-group price-group">
                <label class="form-label" for="price">販売価格</label>
                <input class="form-input price-input" type="number" name="price" id="price" value="{{ old('price') }}">
                <span class="yen">￥</span>
                <p class="error-message">
                    @error('price')
                        {{ $message }}
                    @enderror
                </p>
            </div>
        </section>
        <button class="sell-button" type="submit">出品する</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('image_upload');
    const imagePreviewList = document.getElementById('image_preview_list');

    // ページ読み込み時にセッションに一時画像があれば表示
    const tempImage = "{{ session('temp_image') }}";
    if (tempImage) {
        const div = document.createElement('div');
        div.className = 'reader_file';
        const img = document.createElement('img');
        img.className = 'reader_image';
        img.src = tempImage;
        div.appendChild(img);
        imagePreviewList.appendChild(div);
    }

    imageUpload.addEventListener('change', function(event) {
        initializeFiles(); // 既存のプレビューをクリア
        const files = event.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = (function(theFile) {
                return function(e) {
                    const div = document.createElement('div');
                    div.className = 'reader_file';
                    const img = document.createElement('img');
                    img.className = 'reader_image';
                    img.src = e.target.result;
                    div.appendChild(img);
                    imagePreviewList.appendChild(div);
                };
            })(file);

            reader.readAsDataURL(file);
        }
    });

    function initializeFiles() {
        imagePreviewList.innerHTML = '';
    }
});
</script>
@endsection