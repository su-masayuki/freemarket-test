@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit_profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>

    <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf
        @method('PUT')

        <div class="profile-image-wrapper">
            <img src="{{ $user->image_path ? Storage::url($user->image_path) : asset('images/default-avatar.png') }}" alt="プロフィール画像" class="profile-image">
            <label for="image" class="image-select-label">画像を選択する</label>
            <input type="file" name="image" id="image" class="image-input" hidden>
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label for="zipcode">郵便番号</label>
            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $user->zipcode) }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
        </div>

        <input type="hidden" name="from" value="first">
        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const previewImage = document.querySelector('.profile-image');

        imageInput.addEventListener('change', function () {
            const file = imageInput.files[0];
            if (file) {
                previewImage.src = URL.createObjectURL(file);
            }
        });
    });
</script>
@endsection