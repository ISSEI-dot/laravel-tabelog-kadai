@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-3">
    <div class="w-50">
        <h1>有料プラン登録</h1>
        <hr>

        <form id="payment-form">
            <div id="card-element">
                <!-- クレジットカード入力フィールド -->
            </div>
            <button id="submit" class="btn btn-primary mt-3">サブスクリプションを開始</button>
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
                    fetch('{{ route('subscription.process') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ payment_method: setupIntent.payment_method }),
                    })
                    .then(response => {
                        // レスポンスがOKでない場合はエラーメッセージを表示
                        if (!response.ok) {
                            return response.json().then(err => {
                                alert(err.message || '処理中にエラーが発生しました。');
                                // エラーレスポンスにリダイレクトURLが含まれている場合は遷移
                                if (err.redirect) {
                                    window.location.href = err.redirect; // マイページへ遷移
                                }
                                throw new Error(err.message || '処理中にエラーが発生しました。');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert('サブスクリプションが開始されました！');
                            location.href = "{{ route('mypage') }}";
                        } else {
                            alert(data.message || '処理中にエラーが発生しました。');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || '処理中にエラーが発生しました。');
                    });
                }
            });
        </script>
    </div>
</div>
@endsection
