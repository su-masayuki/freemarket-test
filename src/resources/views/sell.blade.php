@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h2 class="sell-title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-section">
            <label class="section-label">商品画像</label>
            <div class="image-upload-area">
                <label for="image" class="image-upload-label">画像を選択する</label>
                <input type="file" name="image" id="image" hidden>
            </div>
            <div class="image-preview">
                <img id="preview-image" src="" alt="プレビュー画像" style="display: none; max-width: 200px; max-height: 200px;">
            </div>
        </div>

        {{-- 商品の詳細 --}}
        <div class="form-section">
            <h3 class="section-heading">商品の詳細</h3>

            {{-- カテゴリー --}}
            <div class="category-tags">
                @foreach ($categories as $index => $category)
                    <input type="checkbox" name="category[]" value="{{ $category }}" id="category{{ $index }}" {{ in_array($category, old('category', [])) ? 'checked' : '' }} hidden>
                    <label class="category-tag" for="category{{ $index }}">{{ $category }}</label>
                @endforeach
            </div>

            {{-- 商品の状態 --}}
            <div class="form-group">
                <label for="condition">商品の状態</label>
                <select name="condition" id="condition">
                    <option value="">選択してください</option>
                    <option value="新品" {{ old('condition') == '新品' ? 'selected' : '' }}>新品</option>
                    <option value="未使用に近い" {{ old('condition') == '未使用に近い' ? 'selected' : '' }}>未使用に近い</option>
                    <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                    <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                </select>
            </div>
        </div>

        {{-- 商品名と説明 --}}
        <div class="form-section">
            <h3 class="section-heading">商品名と説明</h3>

            <div class="form-group">
                <label for="name">商品名</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="brand_name">ブランド名</label>
                <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
            </div>

            <div class="form-group">
                <label for="description">商品の説明</label>
                <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">販売価格</label>
                <input type="number" name="price" id="price" placeholder="¥" value="{{ old('price') }}">
            </div>
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</div>
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-image');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
});
</script>
@endsection