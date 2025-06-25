@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="address-edit-container">
    <h2 class="address-edit-title">住所の変更</h2>

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
        @csrf
        @method('PUT')

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

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection