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
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // .envのキーを参照

        try {
            // 署名検証
            $payload = $request->getContent(); // リクエスト本文取得
            $sigHeader = $request->header('Stripe-Signature'); // Stripe署名取得
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            // イベントタイプ取得
            $eventType = $event->type;

            switch ($eventType) {
                case 'invoice.payment_succeeded':
                    Log::info('支払い成功');
                    break;

                case 'invoice.payment_failed':
                    Log::warning('支払い失敗');
                    break;

                case 'customer.subscription.deleted':
                    Log::info('サブスクリプションがキャンセルされました。');
                    break;

                default:
                    Log::warning("未対応のイベントタイプ: $eventType");
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook署名検証失敗: ' . $e->getMessage());
            return response()->json(['error' => '署名検証エラー'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook処理エラー: ' . $e->getMessage());
            return response()->json(['error' => 'エラーが発生しました。'], 500);
        }
    }
}
