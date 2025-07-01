@extends('layouts.app')
@php use Illuminate\Support\Str;
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="tab-menu">
        <a href="{{ url('/') }}" class="tab {{ request()->get('page') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ url('/?page=mylist') }}" class="tab {{ request()->get('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="item-grid">
        @foreach ($items as $item)
            @if (!auth()->check() || $item->user_id !== auth()->id())
        <div class="item-card">
            <a href="{{ url('/item/' . $item->id) }}">
                <div class="image-container">
                    @php
                        $imageSrc = Str::startsWith($item->image_path, ['http://', 'https://']) 
                            ? $item->image_path 
                            : asset('storage/' . $item->image_path);
                    @endphp
                    <img src="{{ $imageSrc }}" alt="商品画像">
                    @if ($item->is_sold)
                        <div class="sold-label">SOLD</div>
                    @endif
                </div>
                <div class="item-name">{{ $item->name }}</div>
            </a>
        </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
