@extends('layouts.app')

@push('scripts')
<!-- Stripe.jsのロード -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Stripeの初期化
        const stripe = Stripe("{{ config('services.stripe.key') }}");
        const elements = stripe.elements();
        const cardElement = elements.create('card'); // カード要素作成
        cardElement.mount('#card-element'); // DOMにマウント

        // 2. DOM要素の取得
        const form = document.getElementById('card-form'); // フォーム要素取得
        const cardButton = document.getElementById('card-button'); // ボタン要素取得
        const cardHolderName = document.getElementById('card-holder-name'); // 名前要素取得
        const clientSecret = cardButton.dataset.secret; // シークレットキー取得
        const errorDiv = document.getElementById('card-errors'); // エラー表示領域取得

        // 3. エラーメッセージの処理 (カード入力時)
        cardElement.on('change', function (event) {
            errorDiv.textContent = event.error ? event.error.message : ''; // エラー表示
            errorDiv.style.display = event.error ? 'block' : 'none'; // 表示切り替え
        });

        // 4. フォーム送信処理
        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // デフォルトの送信を停止
            cardButton.disabled = true; // 二重クリック防止

            // 名前が未入力の場合のエラーチェック (前回提案からの追加ポイント)
            if (!cardHolderName.value.trim()) {
                errorDiv.textContent = 'カード名義人を入力してください。';
                errorDiv.style.display = 'block';
                cardButton.disabled = false; // ボタン再有効化
                return; // 処理を終了
            }

            try {
                // Stripeのカード処理 (paymentMethod作成)
                const { error, paymentMethod } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: cardHolderName.value.trim(),
                    },
                });

                // エラー発生時
                if (error) {
                    errorDiv.textContent = error.message;
                    errorDiv.style.display = 'block';
                    cardButton.disabled = false; // ボタン再有効化
                    return; // 処理終了
                }

                // 成功時：トークンをフォームに追加して送信
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'payment_method';
                hiddenInput.value = paymentMethod.id; // 取得したpaymentMethod.idを使用
                form.appendChild(hiddenInput);

                form.submit(); // フォーム送信
            } catch (err) {
                console.error('Stripe処理エラー:', err); // コンソールエラーログ
                errorDiv.textContent = '予期せぬエラーが発生しました。再試行してください。';
                errorDiv.style.display = 'block';
                cardButton.disabled = false; // ボタン再有効化
            }
        });
    });
</script>
@endpush


@section('content')
<div class="container kadai_002-container pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <nav class="my-3" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('mypage') }}">マイページ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">有料プラン登録</li>
                </ol>
            </nav>

            <h1 class="mb-3 text-center">有料プラン登録</h1>

            <!-- 成功メッセージ -->
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- エラーメッセージ -->
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- プラン情報 -->
            <div class="card mb-4">
                <div class="card-header text-center">
                    有料プランの内容
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">・2日前までいつでも予約可能</li>
                    <li class="list-group-item">・店舗をお好きなだけお気に入りに追加可能</li>
                    <li class="list-group-item">・レビューを全件閲覧可能</li>
                    <li class="list-group-item">・レビューを投稿可能</li>
                    <li class="list-group-item">・月額たったの300円</li>
                </ul>
            </div>

            <hr class="mb-4">

            <!-- エラーメッセージ -->
            <div class="alert alert-danger kadai_002-card-error" id="card-error" role="alert" style="display: none;">
                <ul class="mb-0" id="error-list"></ul>
            </div>

            <!-- フォーム -->
            <form id="card-form" action="{{ route('subscription.store') }}" method="post">
                @csrf

                <input class="form-control mb-3" id="card-holder-name" type="text" placeholder="カード名義人" required>
                <div class="kadai_002-card-element mb-4" id="card-element" style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;"></div>
                <div id="card-errors" class="text-danger mb-3" role="alert" style="display: none;"></div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary text-white shadow-sm w-50 kadai_002-btn" id="card-button">
                        サブスクリプションを開始
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
