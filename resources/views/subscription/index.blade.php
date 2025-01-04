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
            <button id="submit" class="btn btn-primary mt-3" disabled>サブスクリプションを開始</button>
            <!-- ローディングインジケーター -->
            <div id="loading-spinner" class="mt-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">処理中...</span>
                </div>
                処理中です。少々お待ちください...
            </div>
        </form>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ env('STRIPE_KEY') }}'); 
            const elements = stripe.elements(); 
            const card = elements.create('card'); 
            card.mount('#card-element'); 

            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit');
            const spinner = document.getElementById('loading-spinner');

            // カード入力後にボタンを有効化
            card.on('change', function(event) {
                submitButton.disabled = event.empty || event.error;
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // ボタン無効化とローディング表示
                submitButton.disabled = true;
                spinner.style.display = 'block';

                const { setupIntent, error } = await stripe.confirmCardSetup(
                    '{{ $intent->client_secret }}',
                    {
                        payment_method: { card: card },
                    }
                );

                if (error) {
                    alert(error.message);
                    // エラー発生時にボタンを再度有効化
                    submitButton.disabled = false;
                    spinner.style.display = 'none';
                } else {
                    fetch('{{ route('subscription.process') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ payment_method: setupIntent.payment_method }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                alert(err.message || '処理中にエラーが発生しました。');
                                if (err.redirect) {
                                    window.location.href = err.redirect;
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
                            submitButton.disabled = false;
                            spinner.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || '処理中にエラーが発生しました。');
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                    });
                }
            });
        </script>
    </div>
</div>
@endsection
