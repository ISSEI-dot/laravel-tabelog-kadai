const stripe = Stripe(stripeKey);
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

const cardHolderName = document.getElementById('card-holder-name');
const cardButton = document.getElementById('card-button');
const clientSecret = cardButton.dataset.secret;

// エラーメッセージを表示するdiv要素を取得する
const cardError = document.getElementById('card-error');
const errorList = document.getElementById('error-list');

cardButton.addEventListener('click', async (e) => {
    e.preventDefault();

    // ボタンを一時的に無効化
    cardButton.disabled = true;

    // クライアントシークレットでセットアップを確認
    const { setupIntent, error } = await stripe.confirmCardSetup(
        clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: { name: cardHolderName.value.trim() } // 空白削除
            }
        }
    );

    // エラー処理
    errorList.innerHTML = ''; // エラーメッセージ初期化

    if (cardHolderName.value.trim() === '') {
        cardError.style.display = 'block';
        let li = document.createElement('li');
        li.textContent = 'カード名義人の入力は必須です。';
        errorList.appendChild(li);
    }

    if (error) {
        console.error(error);
        cardError.style.display = 'block';
        let li = document.createElement('li');
        li.textContent = `エラー: ${error.code} - ${error.message}`;
        errorList.appendChild(li);

        // ボタンを再度有効化
        cardButton.disabled = false;
    } else {
        // 支払いIDをフォームに追加
        stripePaymentIdHandler(setupIntent.payment_method);
    }
});

function stripePaymentIdHandler(paymentMethodId) {
    const form = document.getElementById('card-form');

    const hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'paymentMethodId');
    hiddenInput.setAttribute('value', paymentMethodId);
    form.appendChild(hiddenInput);

    // フォームを送信
    form.submit();
}
