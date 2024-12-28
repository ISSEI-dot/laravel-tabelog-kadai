<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Exceptions\IncompletePayment;


class SubscriptionController extends Controller
{
    // 有料プラン登録ページ
    public function create()
    {
        $user = Auth::user();

        // ページ表示のみ
        return view('subscription.create');

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

        try {
            // 支払い方法を更新
            $user->updateDefaultPaymentMethod($request->paymentMethod);
            // 有料プラン登録
            $user->newSubscription('premium_plan', 'price_1QZOJQBUjnqExYiQySxVRZ74')
                ->create($request->paymentMethod);

                    return redirect()->route('mypage')->with('success', '有料プランへの登録が完了しました。');
                } catch (IncompletePayment $e) {
                    return redirect()->route('cashier.payment', $e->payment->id);
                } catch (\Exception $e) {
                // エラーログの記録
                \Log::error('サブスクリプション登録エラー: ' . $e->getMessage());
        
                // エラーメッセージ表示
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
            // 1. 支払い方法の更新
            $user->updateDefaultPaymentMethod($validated['paymentMethodId']);

            // 2. Stripeから最新のサブスクリプション状態を取得
            $subscription = $user->subscription('premium_plan');
            if (!$subscription) {
                return back()->withErrors(['error' => 'サブスクリプションが見つかりません。']);
            }

            // 3. 更新後の確認
            if ($subscription->valid()) {
                return redirect()->route('mypage')->with('flash_message', 'お支払い方法を変更しました。');
            } else {
                return back()->withErrors(['error' => '支払い方法は更新されましたが、サブスクリプション状態に問題があります。']);
            }
        } catch (\Exception $e) {
            // エラー処理
            \Log::error('支払い方法の更新エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => 'お支払い方法の変更中にエラーが発生しました。']);
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

            // 状態チェック
            if ($subscription->onGracePeriod()) {
                return redirect()->route('mypage')->with('message', '有料プランを解約しました。有効期限終了後にキャンセルされます。');
            } else {
                return redirect()->route('mypage')->with('message', '有料プランを即時解約しました。');
            }

        } catch (\Exception $e) {
            \Log::error('解約エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '解約処理に失敗しました。再度お試しください。']);
        }
    }
}


