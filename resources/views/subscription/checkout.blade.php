@extends('layouts.app')

@section('content')
<div class="container">
    <h2>サブスクリプション登録</h2>
    <form action="{{ route('subscription.store') }}" method="POST" id="payment-form">
        @csrf
        <div class="form-group">
            <label for="card-holder-name">カード名義人</label>
            <input id="card-holder-name" type="text" class="form-control" placeholder="例: 山田 太郎" required>
        </div>
        <div id="card-element" class="form-group mt-3"></div>
        <div id="card-errors" class="text-danger mt-3"></div>
        <button id="card-button" class="btn btn-primary mt-3" data-secret="{{ $intent->client_secret }}">
            サブスクリプションを開始
        </button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('') }}");
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const cardButton = document.getElementById('card-button');
    const cardHolderName = document.getElementById('card-holder-name');
    const clientSecret = cardButton.dataset.secret;

    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();

        const { setupIntent, error } = await stripe.confirmCardSetup(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: cardHolderName.value
                }
            }
        });

        if (error) {
            // エラー表示エリアにエラー内容を表示
            const errorDisplay = document.getElementById('card-errors');
            errorDisplay.textContent = error.message;
        } else {
            const form = document.getElementById('payment-form');
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'paymentMethodId');
            hiddenInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
</script>
@endsection
