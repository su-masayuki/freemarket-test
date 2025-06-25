@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-main">
        <div class="item-summary">
            <img src="{{ asset('storage/images/' . $item->image_path) }}" alt="商品画像" class="item-image">
            <div>
                <p class="item-name">{{ $item->name }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <hr>

        <div class="payment-method">
            <label for="payment">支払い方法</label>
            <select name="payment" id="payment">
                <option value="">選択してください</option>
                <option value="convenience">コンビニ払い</option>
                <option value="card">クレジットカード</option>
            </select>
        </div>

        <hr>

        <div class="delivery-info">
            <div class="delivery-header">
                <p>配送先</p>
                <a href="{{ route('purchase.address.edit', $item) }}">変更する</a>
            </div>
            <p>〒 {{ $user->zipcode }}</p>
            <p>{{ $user->address }} {{ $user->building }}</p>
        </div>

        <hr>
    </div>

    <div class="purchase-summary">
        <div class="summary-box">
            <p>商品代金　¥{{ number_format($item->price) }}</p>
            <p>支払い方法　<span id="payment-method-text">未選択</span></p>
        </div>
        <form action="{{ route('purchase.store', $item->id) }}" method="POST">
            @csrf
            <button type="submit" class="purchase-button">購入する</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentSelect = document.getElementById('payment');
        const paymentMethodText = document.getElementById('payment-method-text');

        paymentSelect.addEventListener('change', function () {
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            const text = selectedOption.text.trim();
            paymentMethodText.textContent = text !== '選択してください' ? text : '未選択';
        });
    });
</script>
@endsection