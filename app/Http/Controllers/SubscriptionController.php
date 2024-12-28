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

    // 有料プラン登録機能
    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            // 支払い方法を更新
            $user->updateDefaultPaymentMethod($request->payment_method);

            // 有料プラン登録
            $user->newSubscription('premium_plan', 'price_1QZOJQBUjnqExYiQySxVRZ74')
             ->create($request->payment_method);

            // 登録完了メッセージ
            return redirect()->route('mypage')->with('success', '有料プランへの登録が完了しました。');
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
            // 支払い未完了の場合は支払いページへリダイレクト
            return redirect()->route('cashier.payment', $e->payment->id);
        } catch (\Exception $e) {
            // エラーログとエラーメッセージ表示
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

        try {
            // 支払い方法の更新
            $user->updateDefaultPaymentMethod($request->payment_method);

            // サブスクリプションの状態をチェック
            $subscription = $user->subscription('premium_plan');
            if (!$subscription) {
                return back()->withErrors(['error' => 'サブスクリプションが見つかりません。']);
            }

            // 更新成功メッセージ
            return redirect()->route('mypage')->with('flash_message', 'お支払い方法を変更しました。');
        } catch (\Exception $e) {
            // エラーログとエラーメッセージ表示
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

            // 解約メッセージ
            return redirect()->route('mypage')->with('message', '有料プランを解約しました。');
        } catch (\Exception $e) {
            \Log::error('解約エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '解約処理に失敗しました。再度お試しください。']);
        }
    }

}


