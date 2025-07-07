@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
<div class="item-detail-container">
    <div class="item-detail-wrapper">
        <div class="item-image">
            @php
                $imageSrc = Str::startsWith($item->image_path, ['http://', 'https://'])
                    ? $item->image_path
                    : asset('storage/' . ltrim($item->image_path, '/'));
            @endphp
            <img src="{{ $imageSrc }}" alt="商品画像">
        </div>

        <div class="item-info">
            <h2 class="item-title">{{ $item->name }}</h2>
            @if (!empty($item->brand_name))
                <p class="brand-name">{{ $item->brand_name }}</p>
            @endif
            <p class="price">¥{{ number_format($item->price) }} <span class="tax">（税込）</span></p>

            <div class="actions">
                <form action="{{ route('items.like', $item->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @php
                        $hasLiked = auth()->user() && auth()->user()->likes->contains($item->id);
                    @endphp
                    <button type="submit" style="background: none; border: none; cursor: pointer;" class="">
                        @if ($hasLiked)
                            <img src="{{ asset('images/like_icon_red.png') }}" alt="いいね" style="width: 20px; height: 20px;">
                        @else
                            <img src="{{ asset('images/like_icon.png') }}" alt="いいね" style="width: 20px; height: 20px;">
                        @endif
                        <span>{{ $item->likes_count }}</span>
                    </button>
                </form>
                <span>
                    <img src="{{ asset('images/comment_icon.png') }}" alt="コメント" style="width: 20px; height: 20px;">
                    {{ $comments->count() }}
                </span>
            </div>

            <form action="{{ url('/purchase/' . $item->id) }}" method="GET">
                <button class="buy-button">購入手続きへ</button>
            </form>

            <div class="item-description">
                <h3>商品説明</h3>
                <p>{!! nl2br(e($item->description)) !!}</p>
            </div>

            <div class="item-meta">
                <h3>商品の情報</h3>
                <div class="item-category">
                    <h3>カテゴリー</h3>
                    <div class="category-tags">
                        @php
                            $categories = is_string($item->category) ? json_decode($item->category, true) : [];
                        @endphp
                        @if (is_array($categories))
                            @foreach ($categories as $cat)
                                <span class="category-tag">{{ $cat }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <p>商品の状態: {{ $item->condition }}</p>
            </div>

            <div class="item-comments">
                <h3>コメント({{ $comments->count() }})</h3>
                @foreach ($comments as $comment)
                    <div class="comment">
                        @php
                            $profileImage = $comment->user->image_path ? asset('storage/' . ltrim($comment->user->image_path, '/')) : asset('images/default-avatar.png');
                        @endphp
                        <img src="{{ $profileImage }}" alt="user" class="comment-avatar">
                        <div>
                            <strong>{{ $comment->user->name }}</strong>
                            <p>{{ $comment->body }}</p>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form">
                    @csrf
                    <label for="comment">商品へのコメント</label>
                    <textarea name="body" id="comment" rows="3"></textarea>
                    <button type="submit" class="submit-comment">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection