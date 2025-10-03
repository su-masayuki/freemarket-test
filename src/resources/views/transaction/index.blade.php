@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
@endsection

@section('content')
<div class="transaction-container">
    <div class="transaction-sidebar">
        <h3>その他の取引</h3>
        <ul class="transaction-list">
            @foreach($tradingItems as $tradingItem)
                <li>
                    <a href="{{ route('transaction_messages.index', $tradingItem->id) }}">
                        {{ $tradingItem->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="transaction-main">
        <div class="transaction-top">
            @if (! $isSeller && ! $buyerCompleted)
                <form method="POST" action="{{ route('transactions.complete', $item->id) }}">
                    @csrf
                    <button type="submit" class="btn-complete">取引を完了する</button>
                </form>
            @endif
        </div>

        <h2 class="transaction-title">
            「{{ $item->user_id === Auth::id() 
                ? optional(optional($item->purchases->first())->user)->name 
                : optional($item->user)->name }}」さんとの取引画面
        </h2>

        <div class="transaction-header">
            <div class="item-info">
                <div class="item-image">
                    @php
                        $itemImage = $item->image_path;
                        $itemImageUrl = Str::startsWith($itemImage, 'http')
                            ? $itemImage
                            : Storage::url($itemImage);
                    @endphp
                    <img src="{{ $itemImageUrl }}" alt="商品画像">
                </div>
                <div class="item-detail">
                    <h3>{{ $item->name }}</h3>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        <div class="chat-area">
            @foreach($messages->reverse() as $message)
                <div class="chat-message {{ $message->sender_id === Auth::id() ? 'my-message' : 'partner-message' }}" style="{{ $message->sender_id === Auth::id() ? 'text-align: right;' : 'text-align: left;' }}">
                    <div class="chat-user">
                        @php
                            $profileImage = $message->sender->profile_image ?? 'default.png';
                            $profileImageUrl = Str::startsWith($profileImage, 'http')
                                ? $profileImage
                                : Storage::url($profileImage);
                        @endphp
                        <img src="{{ $profileImageUrl }}" class="chat-icon">
                        <span class="chat-username">{{ $message->sender->name }}</span>
                    </div>
                    <div class="chat-body">
                        @if($message->message)
                            <p>{{ $message->message }}</p>
                        @endif
                        @if($message->image_path)
                            @php
                                $msgImage = $message->image_path;
                                $msgImageUrl = Str::startsWith($msgImage, 'http')
                                    ? $msgImage
                                    : Storage::url($msgImage);
                            @endphp
                            <img src="{{ $msgImageUrl }}" class="chat-image">
                        @endif
                    </div>
                    @if($message->sender_id === Auth::id() && $message->id === $messages->first()->id)
                        <div class="chat-actions">
                            <a href="javascript:void(0)" onclick="editMessage({{ $message->id }}, '{{ $message->message }}')">編集</a>
                            <form action="{{ route('transaction_messages.destroy', ['item' => $item->id, 'message' => $message->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">削除</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color:red;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="chat-form">
            <form id="chat-form" action="{{ route('transaction_messages.store', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="message_id" id="edit-message-id">
                <input type="text" name="message" id="chat-message-input" placeholder="取引メッセージを記入してください" maxlength="500" value="{{ old('message') }}" style="width: 80%;">
                <label for="image-upload" class="btn-add-image">画像を追加</label>
                <input id="image-upload" type="file" name="image" accept="image/*" style="display:none;">
                <button type="submit" class="btn-submit">
                    <img src="{{ asset('images/send-icon.jpg') }}" alt="送信" class="send-icon">
                </button>
            </form>
        </div>
    </div>

    <div id="rating-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>取引が完了しました。</h3>
            <p>今回の取引相手はどうでしたか？</p>
            <form id="rating-form" action="{{ route('transactions.rate', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="target_user_id"
                       value="{{ $isSeller ? optional($item->purchases->first())->user_id : $item->user_id }}">
                <div class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star" data-value="{{ $i }}">&#9733;</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-value">
                <button type="submit" class="btn-rate" id="btn-rate">送信する</button>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function editMessage(messageId, messageText) {
        document.getElementById('edit-message-id').value = messageId;
        document.getElementById('chat-message-input').value = messageText;
        const form = document.getElementById('chat-form');
        form.action = `/items/{{ $item->id }}/messages/${messageId}`;
        if (!document.getElementById('edit-method')) {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_method';
            input.value = 'PUT';
            input.id = 'edit-method';
            form.appendChild(input);
        }
    }
    window.editMessage = editMessage;

    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            document.getElementById('rating-value').value = value;
            document.querySelectorAll('.star').forEach(s => s.classList.remove('selected'));
            for (let i = 0; i < value; i++) {
                document.querySelectorAll('.star')[i].classList.add('selected');
            }
        });
    });

    @if (session('showRatingModal') || session('showSellerRatingModal') || ($isSeller && $buyerCompleted && !$sellerCompleted))
        document.getElementById('rating-modal').style.display = 'flex';
    @endif

    const ratingForm = document.getElementById('rating-form');
    if (ratingForm) {
        ratingForm.addEventListener('submit', function() {
            console.log('rating form submitted');
        });
    }

    const chatInput = document.getElementById('chat-message-input');
    const storageKey = "chat-message-input-{{ $item->id }}";

    if (sessionStorage.getItem(storageKey)) {
        chatInput.value = sessionStorage.getItem(storageKey);
    }

    chatInput.addEventListener('input', function() {
        sessionStorage.setItem(storageKey, chatInput.value);
    });

    const chatForm = document.getElementById('chat-form');
    chatForm.addEventListener('submit', function() {
        sessionStorage.removeItem(storageKey);
    });

    const chatArea = document.querySelector('.chat-area');
    if (chatArea) {
        chatArea.scrollTop = chatArea.scrollHeight;
    }
});
</script>
@endsection