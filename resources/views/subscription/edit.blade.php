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
            <button id="submit" class="btn btn-primary mt-3" disabled>お支払い方法を更新</button> 
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

                // ボタンを無効化し、ローディングインジケーターを表示
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
                    // ボタンを再度有効化し、ローディングインジケーターを非表示にする
                    submitButton.disabled = false;
                    spinner.style.display = 'none';
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
                            submitButton.disabled = false;
                            spinner.style.display = 'none';
                        } 
                    }) 
                    .catch(error => { 
                        console.error('Error:', error); 
                        alert('処理中にエラーが発生しました。'); 
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                    }); 
                } 
            }); 
        </script> 
    </div> 
</div> 
@endsection
