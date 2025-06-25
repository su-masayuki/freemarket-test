@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="profile-header">
        <div class="avatar-circle">
            <img src="{{ Auth::user()->image_path ? Storage::url(Auth::user()->image_path) : asset('images/default-avatar.png') }}" alt="プロフィール画像">
        </div>
        <div class="profile-info">
            <h2 class="user-name">{{ Auth::user()->name }}</h2>
            <a href="{{ route('mypage.profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="tab-menu">
        <a href="{{ url('/mypage?page=sell') }}" class="tab {{ request('page') !== 'buy' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('/mypage?page=buy') }}" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="item-grid">
        @foreach ($items as $item)
            <a href="{{ url('/item/' . $item->id) }}" class="item-card">
                <div class="image-container">
                    <img src="{{ asset('storage/images/' . $item->image_path) }}" alt="商品画像">
                    @if ($item->is_sold)
                        <div class="sold-label">SOLD</div>
                    @endif
                </div>
                <div class="item-name">{{ $item->name }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection