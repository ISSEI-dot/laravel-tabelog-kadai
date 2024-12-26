<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;


class SubscriptionController extends Controller
{
    // 有料プラン登録ページ
    public function create()
    {
        $user = Auth::user();

        // Stripe顧客IDが存在しない場合、新規作成
        if (!$user->stripe_id) {
            $stripeCustomer = $user->createAsStripeCustomer();
        }

        // SetupIntentを作成
        $intent = $user->createSetupIntent();

        return view('subscription.create', compact('intent'));


        // ユーザーがログインしていることを確認
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // createSetupIntentが存在するか確認
        if (!method_exists($user, 'createSetupIntent')) {
            abort(500, 'SetupIntentを作成することができません。');
        }

        // SetupIntentを作成
        $intent = $user->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    // 有料プラン登録機能
    public function store(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'paymentMethodId' => 'required|string',
        ]);

        // 既に登録済みか確認
        if ($user->subscribed('premium_plan')) {
        return redirect()->route('mypage')->with('flash_message', '既に有料プランに登録済みです。');
        }

        try {
            // 有料プラン登録
            $user->newSubscription('premium_plan', 'price_1QZOJQBUjnqExYiQySxVRZ74')
                ->create($request->paymentMethodId);

            // **ここでステータスを強制的に「active」に更新**
            $user->subscriptions()->update([
                'stripe_status' => 'active'
            ]);

            return redirect()->route('mypage')->with('flash_message', '有料プランへの登録が完了しました。');
        } catch (\Exception $e) {
            // エラーログを記録
            \Log::error('有料プラン登録エラー: ' . $e->getMessage(), [
            'user_id' => $user->id,
            'payment_method_id' => $request->paymentMethodId,
            ]);

            // エラーメッセージを表示
            return back()->withErrors(['error' => '登録中に問題が発生しました。再度お試しください。']);
        }
    }


    // お支払い方法編集ページ
    public function edit()
    {
        $user = Auth::user();

        // ユーザーが存在しない場合にエラーを返す
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'ログインしてください。']);
        }

        try {
            // SetupIntentを作成
            $intent = $user->createSetupIntent();
            return view('subscription.edit', compact('user', 'intent'));
        } catch (\Exception $e) {
            // エラーをログに記録
            \Log::error('SetupIntentの作成中にエラーが発生しました: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            // エラーメッセージをユーザーに表示
            return back()->withErrors(['error' => '支払い方法編集ページを表示できませんでした。再度お試しください。']);
        }
    }


    // お支払い方法更新機能
    public function update(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $validated = $request->validate([
            'paymentMethodId' => 'required|string',
        ]);

        try {
            // 支払い方法を更新
            $user->updateDefaultPaymentMethod($validated['paymentMethodId']);

            $user->subscriptions()->update([
                'stripe_status' => 'active'
            ]);

            return redirect()->route('mypage')->with('flash_message', 'お支払い方法を変更しました。');
        } catch (\Exception $e) {
            // エラーをログに記録
            \Log::error('支払い方法の更新中にエラーが発生しました: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            // エラーメッセージをユーザーに表示
            return back()->withErrors(['error' => 'お支払い方法の変更中にエラーが発生しました。再度お試しください。']);
        }
    }


    // 有料プラン解約ページ
    public function cancel()
    {
        $user = Auth::user();

        // サブスクリプション情報を取得
        $subscription = $user->subscription('premium_plan');

        // サブスクリプションが存在しない場合のエラーハンドリング
        if (!$subscription) {
            return redirect()->route('mypage')->withErrors(['error' => '現在、有料プランには登録されていません。']);
        }

        return view('subscription.cancel', compact('subscription'));
    }

    // 有料プラン解約機能
    public function destroy()
    {
        $user = Auth::user();

        try {
            // サブスクリプションの取得
            $subscription = $user->subscription('premium_plan');

            if (!$subscription || $subscription->cancelled()) {
                return redirect()->route('mypage')->withErrors(['error' => '有料プランに未登録、または既に解約済みです。']);
            }

            // サブスクリプションを即時解約
            $subscription->cancelNow();

            // ステータスを強制的に更新
            $user->subscriptions()->update([
                'stripe_status' => 'canceled'
            ]);

                return redirect()->route('mypage')->with('flash_message', '有料プランを解約しました。');
            } catch (\Exception $e) {
            // エラーをログに記録
            \Log::error('サブスクリプション解約中にエラーが発生しました: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return back()->withErrors(['error' => '解約処理中に問題が発生しました。再度お試しください。']);
        }
    }

}
