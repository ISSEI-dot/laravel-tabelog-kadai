<!DOCTYPE html>
<html>
<head>
    <title>Subscription</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <form id="subscription-form">
        <div id="payment-element">
            <!-- Stripeがカード入力フォームを表示 -->
        </div>
        <button id="submit">Subscribe</button>
    </form>

    <script>
        const stripe = Stripe('your-stripe-public-key');

        async function initialize() {
            const response = await fetch('/create-setup-intent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });
            const { clientSecret } = await response.json();

            const elements = stripe.elements();
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
        }

        initialize();
    </script>
</body>
</html>
