@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-3">
    <div class="w-50">
        <h1>お支払い方法編集</h1>
        <hr>

        <form id="payment-form">
            <div id="card-element">
                <!-- クレジットカード入力フィールド -->
            </div>
            <button id="submit" class="btn btn-primary mt-3">お支払い方法を更新</button>
        </form>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
            const elements = stripe.elements();
            const card = elements.create('card');
            card.mount('#card-element');

            const form = document.getElementById('payment-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    '{{ $intent->client_secret }}',
                    {
                        payment_method: { card: card },
                    }
                );

                if (error) {
                    alert(error.message);
                } else {
                    fetch('{{ route('subscription.update') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ payment_method: setupIntent.payment_method }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert('お支払い方法が更新されました！');
                            location.href = "{{ route('mypage') }}";
                        } else {
                            alert('処理中にエラーが発生しました。');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('処理中にエラーが発生しました。');
                    });
                }
            });
        </script>
    </div>
</div>
@endsection
