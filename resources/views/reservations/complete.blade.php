@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="text-success">予約が完了しました！</h2>
    <p>以下の内容で予約を受け付けました。</p>

    <div class="card p-4 mt-4">
        <p><strong>店舗名:</strong> {{ $reservation->product->name }}</p>
        <p><strong>お名前:</strong> {{ $reservation->customer_name }}</p>
        <p><strong>人数:</strong> {{ $reservation->people_count }}人</p>
        <p><strong>予約日:</strong> {{ $reservation->reservation_date }}</p>
        <p><strong>予約時間:</strong> {{ $reservation->reservation_time }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('top') }}" class="btn btn-primary">トップページへ戻る</a>
        <a href="{{ route('mypage.reservations') }}" class="btn btn-secondary">予約履歴を見る</a>
    </div>
</div>
@endsection
