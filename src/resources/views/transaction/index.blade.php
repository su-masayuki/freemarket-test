

@extends('layouts.app')

@section('content')
<div class="transaction-container">
    {{-- 商品情報 --}}
    <div class="transaction-header">
        <div class="item-info">
            <img src="{{ Storage::url($item->image_path) }}" alt="商品画像" class="item-image">
            <div class="item-detail">
                <h2>{{ $item->name }}</h2>
                <p>¥{{ number_format($item->price) }}</p>
            </div>
        </div>
        {{-- 出品者だけが表示する取引完了ボタン --}}
        @if ($isSeller)
            <form method="POST" action="{{ route('transactions.complete', $item->id) }}">
                @csrf
                <button type="submit" class="btn-complete">取引を完了する</button>
            </form>
        @endif
    </div>

    {{-- チャットエリア --}}
    <div class="chat-area">
        @foreach($messages as $message)
            <div class="chat-message {{ $message->sender_id === Auth::id() ? 'my-message' : 'partner-message' }}">
                <div class="chat-body">
                    @if($message->message)
                        <p>{{ $message->message }}</p>
                    @endif
                    @if($message->image_path)
                        <img src="{{ Storage::url($message->image_path) }}" class="chat-image">
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- 入力フォーム --}}
    <div class="chat-form">
        <form action="{{ route('transaction_messages.store', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="message" placeholder="取引メッセージを記入してください" maxlength="400">
            <input type="file" name="image" accept="image/*">
            <button type="submit">送信</button>
        </form>
    </div>
</div>
@endsection