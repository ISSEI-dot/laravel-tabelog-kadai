<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SubscriptionController extends Controller
{
    // 有料プラン登録ページ
    public function create()
    {
        $user = Auth::user();

        // Stripe顧客IDが存在しない場合、新規作成
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        // SetupIntentを作成
        $intent = $user->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    // チェックアウトページ表示
    public function showCheckoutForm()
    {
        $user = Auth::user();

        // Stripe顧客が未登録の場合は作成
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        // SetupIntentを作成
        $intent = $user->createSetupIntent();

        return view('subscription.checkout', compact('intent'));
    }


    // 有料プラン登録機能
    public function store(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'paymentMethodId' => 'required|string',
        ]);

        try {
            // 支払い方法を更新
            $user->updateDefaultPaymentMethod($request->paymentMethodId);
            // 有料プラン登録
            $user->newSubscription('premium_plan', 'price_1QZOJQBUjnqExYiQySxVRZ74')
                ->create($request->paymentMethodId);

            return redirect()->route('mypage')->with('success', '有料プランへの登録が完了しました。');
        } catch (\Exception $e) {
            // エラーログを記録
            \Log::error('サブスクリプション登録エラー: ' . $e->getMessage());
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
            return redirect()->route('mypage')->withErrors(['status' => '現在、有料プランには登録されていません。']);
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

            if (!$subscription) {
                return back()->withErrors(['error' => '登録されていません。']);
            }

            // サブスクリプションを解約
            $subscription->cancel();

            // ステータスを強制的に更新
            $user->subscriptions()->update([
                'stripe_status' => 'canceled'
            ]);

                return redirect()->route('mypage')->with('message', '有料プランを解約しました。');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
