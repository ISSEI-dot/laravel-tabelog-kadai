<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 環境変数からシークレットキー取得
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        if (!$endpointSecret) {
            Log::error('Webhookシークレットキーが設定されていません。');
            return response()->json(['error' => 'サーバー設定エラー'], 500);
        }

        // 署名とペイロードを取得
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            // Stripeイベントの検証と解析
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            // イベントタイプの取得
            $eventType = $event->type;

            switch ($eventType) {
                case 'invoice.payment_succeeded':
                    Log::info('支払い成功: ' . json_encode($event));
                    break;

                case 'invoice.payment_failed':
                    Log::warning('支払い失敗: ' . json_encode($event));
                    break;

                case 'customer.subscription.deleted':
                    Log::info('サブスクリプションがキャンセルされました: ' . json_encode($event));
                    break;

                case 'customer.created':
                    Log::info('新規顧客が作成されました: ' . json_encode($event));
                    break;

                case 'customer.updated':
                    Log::info('顧客情報が更新されました: ' . json_encode($event));
                    break;

                case 'checkout.session.completed':
                    Log::info('チェックアウト完了: ' . json_encode($event));
                    break;

                default:
                    Log::warning("未対応のイベントタイプ: $eventType");
                    break;
            }

            // 正常応答
            return response()->json(['status' => 'success']);
        } catch (SignatureVerificationException $e) {
            // 署名検証失敗エラーハンドリング
            Log::error('Webhook署名検証失敗: ' . $e->getMessage());
            return response()->json(['error' => '署名検証エラー'], 400);
        } catch (\Exception $e) {
            // その他のエラーハンドリング
            Log::error('Webhook処理エラー: ' . $e->getMessage());
            return response()->json(['error' => 'エラーが発生しました。'], 500);
        }
    }
}
