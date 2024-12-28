@extends('layouts.app')

@push('scripts')
<!-- Stripe.jsのロード -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe("{{ config('services.stripe.key') }}");
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('card-form');
        const cardButton = document.getElementById('card-button');
        const cardHolderName = document.getElementById('card-holder-name');
        const clientSecret = cardButton.dataset.secret;
        const errorDiv = document.getElementById('card-errors');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            cardButton.disabled = true; // 二重送信防止

            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret,
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardHolderName.value.trim(),
                        },
                    },
                }
            );

            if (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
                cardButton.disabled = false;
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'payment_method';
                hiddenInput.value = setupIntent.payment_method;
                form.appendChild(hiddenInput);
                form.submit();
            }
        });

        cardElement.on('change', function (event) {
            errorDiv.textContent = event.error ? event.error.message : '';
            errorDiv.style.display = event.error ? 'block' : 'none';
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

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <hr>

            @if (session('subscription_message'))
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">{{ session('subscription_message') }}</p>
                </div>
            @endif

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

            <div class="alert alert-danger kadai_002-card-error" id="card-error" role="alert" style="display: none;">
                <ul class="mb-0" id="error-list"></ul>
            </div>

            <form id="card-form" action="{{ route('subscription.store') }}" method="post">
                @csrf

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <input class="form-control mb-3" id="card-holder-name" type="text" placeholder="カード名義人" required>
                <div class="kadai_002-card-element mb-4" id="card-element" style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;"></div>
                <div id="card-errors" class="text-danger mb-3" role="alert" style="display: none;"></div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary text-white shadow-sm w-50 kadai_002-btn" id="card-button" data-secret="">
                        サブスクリプションを開始
                    </button>
                </div>
            </form>           
        </div>
    </div>
</div>
@endsection
