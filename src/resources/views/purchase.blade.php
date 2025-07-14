@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.store', $item->id) }}" method="POST">
    @csrf
    <div class="purchase-container">
        <div class="purchase-main">
            <div class="item-summary">
                @php
                    use Illuminate\Support\Str;
                    $imageSrc = Str::startsWith($item->image_path, ['http://', 'https://'])
                        ? $item->image_path
                        : asset('storage/' . $item->image_path);
                @endphp
                <img src="{{ $imageSrc }}" alt="商品画像" class="item-image">
                <div>
                    <p class="item-name">{{ $item->name }}</p>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr>

            <div class="payment-method">
                <label for="payment">支払い方法</label>
                <select name="payment_method" id="payment">
                    <option value="">選択してください</option>
                    <option value="convenience">コンビニ払い</option>
                    <option value="card">クレジットカード</option>
                </select>
                @if ($errors->has('payment_method'))
                    <p class="error-message">{{ $errors->first('payment_method') }}</p>
                @endif
            </div>

            <hr>

            <div class="delivery-info">
                <div class="delivery-header">
                    <p>配送先</p>
                    <a href="{{ route('purchase.address.edit', $item) }}">変更する</a>
                </div>
                <p>〒 {{ old('zipcode', $user->zipcode) }}</p>
                @if ($errors->has('zipcode'))
                    <p class="error-message">{{ $errors->first('zipcode') }}</p>
                @endif
                <p>{{ old('address', $user->address) }} {{ old('building', $user->building) }}</p>
                @if ($errors->has('address'))
                    <p class="error-message">{{ $errors->first('address') }}</p>
                @endif
                @if ($errors->has('building'))
                    <p class="error-message">{{ $errors->first('building') }}</p>
                @endif
            </div>

            <input type="hidden" name="zipcode" id="zipcode" value="{{ old('zipcode', $user->zipcode) }}">
            <input type="hidden" name="address" id="address" value="{{ old('address', $user->address) }}">
            <input type="hidden" name="building" id="building" value="{{ old('building', $user->building) }}">

            <hr>
        </div>

        <div class="purchase-summary">
            <div class="summary-box">
                <p>商品代金　¥{{ number_format($item->price) }}</p>
                <p>支払い方法　<span id="payment-method-text">未選択</span></p>
            </div>
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </div>
</form>
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