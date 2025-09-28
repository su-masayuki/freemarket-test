@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp
@php use Illuminate\Support\Str; @endphp

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
            <div class="user-rating">
                @php
                    $averageRating = $averageRating ?? 0;
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $averageRating)
                        <span class="star filled">★</span>
                    @else
                        <span class="star">☆</span>
                    @endif
                @endfor
            </div>
        </div>
        <a href="{{ route('mypage.profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
    </div>

    <div class="tab-menu">
        <a href="{{ url('/mypage?page=sell') }}" class="tab {{ request('page') !== 'buy' && request('page') !== 'trading' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ url('/mypage?page=buy') }}" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">購入した商品</a>
        @php
            $totalUnreadCount = DB::table('transaction_messages')
                ->whereIn('item_id', $tradingItems->pluck('id'))
                ->whereNotIn('id', function ($query) {
                    $query->select('message_id')
                        ->from('transaction_messages_reads')
                        ->where('user_id', Auth::id());
                })
                ->count();
        @endphp
        <a href="{{ url('/mypage?page=trading') }}" class="tab {{ request('page') === 'trading' ? 'active' : '' }}">
            取引中の商品 <span class="badge">{{ $totalUnreadCount }}</span>
        </a>
    </div>

    <div class="item-grid">
        @php
            $items = $items->sortByDesc(function ($item) {
                return optional($item->messages->last())->created_at;
            });
        @endphp
        @foreach ($items as $item)
            <a href="{{ request('page') === 'trading' 
                        ? route('transaction_messages.index', $item->id) 
                        : url('/item/' . $item->id) }}" 
               class="item-card">
                <div class="image-container">
                    @php
                        $imageSrc = Str::startsWith($item->image_path, ['http://', 'https://']) 
                            ? $item->image_path 
                            : Storage::url($item->image_path);
                    @endphp
                    <img src="{{ $imageSrc }}" alt="商品画像">
                    @php
                        $unreadCount = DB::table('transaction_messages')
                            ->where('item_id', $item->id)
                            ->whereNotIn('id', function ($query) {
                                $query->select('message_id')
                                    ->from('transaction_messages_reads')
                                    ->where('user_id', Auth::id());
                            })
                            ->count();
                    @endphp
                    @if (request('page') === 'trading' && $unreadCount > 0)
                        <span class="trade-badge">{{ $unreadCount }}</span>
                    @endif
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