<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id', // 追加
        'customer_name',
        'people_count',
        'reservation_date',
        'reservation_time',
    ];

    // タイムスタンプが不要な場合は無効化
    public $timestamps = true;

    // リレーション：Productモデル
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // リレーション：Userモデル
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // アクセサ例：日付フォーマット変更
    public function getFormattedReservationDateAttribute()
    {
        return \Carbon\Carbon::parse($this->reservation_date)->format('Y/m/d');
    }

    // スコープ例：ユーザーごとの予約
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
