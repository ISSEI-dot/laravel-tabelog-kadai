<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // 予約フォーム表示
    public function create(Product $product)
    {
        $regular_holidays = $product->regular_holiday ? explode(',', $product->regular_holiday) : [];
        return view('reservations.create', compact('product', 'regular_holidays'));

    }

    // 予約を保存
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'people_count' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
        ]);

        // 定休日のチェック
        $reservation_date = Carbon::parse($validated['reservation_date']);
        
        // 日本語表記の曜日対応
        $weekdays = [
            'Monday' => '月曜日',
            'Tuesday' => '火曜日',
            'Wednesday' => '水曜日',
            'Thursday' => '木曜日',
            'Friday' => '金曜日',
            'Saturday' => '土曜日',
            'Sunday' => '日曜日',
        ];

        $reservation_day = $weekdays[$reservation_date->format('l')]; // 曜日を日本語表記に変換
        $regular_holidays = explode(',', $product->regular_holiday); // データベースの定休日リストを取得

        if (in_array($reservation_day, $regular_holidays)) {
            return back()->withErrors(['error' => 'この店舗は定休日です。']);
        }

        // 営業時間のチェック
        $reservation_time = Carbon::parse($validated['reservation_time']);
        $opening_time = Carbon::parse($product->opening_time);
        $closing_time = Carbon::parse($product->closing_time);

        if ($reservation_time->lt($opening_time) || $reservation_time->gt($closing_time)) {
            return back()->withErrors(['error' => '営業時間外の予約はできません。']);
        }

        $reservation = Reservation::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(), // ログイン中のユーザーIDを保存
            'customer_name' => $validated['customer_name'],
            'people_count' => $validated['people_count'],
            'reservation_date' => $reservation_date->toDateString(),
            'reservation_time' => $reservation_time->toTimeString(),
        ]);

        return redirect()->route('reservations.complete', ['reservation' => $reservation->id])
                     ->with('success', '予約が完了しました。');

                         
    }

    public function index()
    {
        // ログイン中のユーザーが関連する product_id の予約を取得
        // 例: `products` テーブルでユーザーと関連付けされた user_id を利用する場合
        $reservations = Reservation::where('user_id', auth()->id())->orderBy('reservation_date', 'desc')
        ->paginate(9); // ページネーションを追加

        // ビューにデータを渡して表示
        return view('reservations.index', compact('reservations'));
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        // キャンセル期限のチェック: 前日までキャンセル可能
        $now = Carbon::now();
        $reservation_date = Carbon::parse($reservation->reservation_date);

        if ($now->greaterThanOrEqualTo($reservation_date->copy()->subDay())) {
        return redirect()->route('reservations.index')->with('error', '予約は前日までしかキャンセルできません。');
    }

    // 予約を削除（キャンセル）
    $reservation->delete();

    return redirect()->route('reservations.index')->with('success', '予約をキャンセルしました。');
    }

    // 予約完了画面
    public function complete(Reservation $reservation)
    {
        return view('reservations.complete', compact('reservation'));
    }

}
