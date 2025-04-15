@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/edit.css')}}">
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
<div class="profile-edit-container">
    <h2 class="purofile-edit-title">
        @isset($profile)    
            プロフィール編集
        @else
            プロフィール設定
        @endisset
    </h2>
    <form class="profile-edit-form" action="/mypage/profile" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($profile)
            @method('PATCH')
        @endisset
        <div class="form-group">
            <div class="image-upload">
                <div class="profile-image" style="@isset($profile) background-image: url('{{ asset('storage/' . $profile->profile_image_path) }}'); @endisset"></div>
                <label class="image-upload-label" for="profile-image">画像を選択する</label>
                <input class="image-upload-input" type="file" name="profile-image" id="profile-image">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="username">ユーザー名</label>
            <input class="form-input" type="text" name="username" id="username" value="{{ $profile ? $profile->name : auth()->user()->name }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="postal_code">郵便番号</label>
            <input class="form-input" type="text" name="postal_code" id="postal_code" value="{{ $profile ? $profile->postal_code : '' }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="address">住所</label>
            <input class="form-input" type="text" name="address" id="address" value="{{ $profile ? $profile->address : '' }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="building">建物名</label>
            <input class="form-input" type="text" name="building" id="building" value="{{ $profile ? $profile->building : '' }}">
        </div>
        <button class="update-button" type="submit">
            @isset($profile)
                更新する
            @else
                設定する
            @endisset
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('profile_image');
        const imagePreview = document.querySelector('.profile-image-preview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.style.backgroundImage = `url(${e.target.result})`;
                    imagePreview.style.backgroundColor = 'transparent';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.backgroundImage = '';
                imagePreview.style.backgroundColor = '#ddd';
            }
        });
    });
</script>
@endsection

