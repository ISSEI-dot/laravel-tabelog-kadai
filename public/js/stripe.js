const stripe = Stripe("pk_test_51QRRhbBUjnqExYiQXTuTOwhbcr8tKPjeSjqE3sQriC04w7LLhGDTaDWSRk0eigUyO44fbQxhKW8HXDb3cjmu3q3J00kS1Dgnpx");
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

const cardHolderName = document.getElementById('card-holder-name');
const cardButton = document.getElementById('card-button');
const clientSecret = cardButton.dataset.secret;

// エラーメッセージ表示要素
const cardError = document.getElementById('card-error');
const errorList = document.getElementById('error-list');

// ボタンクリック時の処理
cardButton.addEventListener('click', async (e) => {
    e.preventDefault();

    // ボタン無効化
    cardButton.disabled = true;

    // エラーメッセージ初期化
    errorList.innerHTML = '';
    cardError.style.display = 'none';

    // 名義人チェック
    if (cardHolderName.value.trim() === '') {
        displayError('カード名義人の入力は必須です。');
        cardButton.disabled = false; // ボタン再有効化
        return;
    }

    // Stripeのセットアップ確認
    const { setupIntent, error } = await stripe.confirmCardSetup(
        clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: { name: cardHolderName.value.trim() }
            }
        }
    );

    if (error) {
        // エラー表示
        displayError(`エラー: ${error.code} - ${error.message}`);
        cardButton.disabled = false; // ボタン再有効化
    } else {
        // 支払いIDをフォームに追加して送信
        stripePaymentIdHandler(setupIntent.payment_method);
    }
});

// エラーメッセージ表示関数
function displayError(message) {
    cardError.style.display = 'block';
    let li = document.createElement('li');
    li.textContent = message;
    errorList.appendChild(li);
}

// 支払いIDフォーム追加関数
function stripePaymentIdHandler(paymentMethodId) {
    const form = document.getElementById('card-form');

    const hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'paymentMethodId');
    hiddenInput.setAttribute('value', paymentMethodId);
    form.appendChild(hiddenInput);

    // フォーム送信
    form.submit();
}
