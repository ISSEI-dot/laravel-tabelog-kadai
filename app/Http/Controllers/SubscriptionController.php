<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    // 支払い方法をセットアップするためのビューページを表示
    public function showSubscriptionPage()
    {
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.index', compact('intent'));
    }

    // サブスクリプションの作成
    public function processSubscription(Request $request)
    {
        try {
            $user = Auth::user();

            // 既に登録済みか確認
            if ($user->subscribed('default')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'すでにサブスクリプションに登録済みです。',
                    'redirect' => route('mypage') // マイページのURLを返す
                ], 400); // HTTP 400 (Bad Request)を返す
            }

            // Stripe顧客を作成
            $user->createOrGetStripeCustomer();

            // 支払い方法の追加
            $paymentMethod = $request->payment_method;
            $user->addPaymentMethod($paymentMethod);

            // サブスクリプション作成
            $user->newSubscription('default', 'price_1QZOJQBUjnqExYiQySxVRZ74') 
                ->create($paymentMethod);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Subscription Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }  
    }

    public function showEditPaymentPage()
    {
        $intent = auth()->user()->createSetupIntent(); // 支払い情報設定用のインテント作成
        return view('subscription.edit', compact('intent')); // ビューにインテントを渡す
    }

    public function updatePaymentMethod(Request $request)
    {
        try {
            // 認証ユーザーを取得
            $user = Auth::user();

            // 支払い方法を更新
            $user->updateDefaultPaymentMethod($request->payment_method);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            // エラーログを記録
            \Log::error('Payment Update Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // サブスクリプションの即時解約
    public function cancelSubscription()
    {
        $user = Auth::user();

        // サブスクリプションを即時解約し、Stripeからも削除
        if ($user->subscribed('default')) {
            $user->subscription('default')->cancelNow(); // 即時解約
        }

        //サブスクリプションアイテムの削除
        DB::table('subscription_items')
            ->whereIn('subscription_id', $user->subscriptions()->pluck('id'))
            ->delete();

        // Stripeの顧客情報も完全に削除
        $stripeCustomer = $user->asStripeCustomer();
        $stripeCustomer->delete(); // Stripe APIで削除

        // データベース上の支払い情報をリセット
        $user->forceFill([
            'stripe_id' => null,
            'pm_type' => null,
            'pm_last_four' => null,
        ])->save();

        // subscriptions テーブルの削除
        $user->subscriptions()->delete(); 

        // 予約履歴とお気に入りも削除
        $user->reservations()->delete();
        $user->favorites()->delete();

        // 解約完了メッセージとともに遷移
        return redirect()->route('mypage')->with('status', 'サブスクリプションは正常に解約されました。');
    }

    // サブスクリプションのステータス確認
    public function checkSubscriptionStatus()
    {
        $user = Auth::user();
        if ($user->subscribed('default')) {
            return response()->json(['status' => 'active']);
        }

        return response()->json(['status' => 'inactive']);
    }
}
