@extends('layouts.app')

@section('content')

<div class="container">
    <span>
    <a href="{{ route('mypage') }}">マイページ</a> > パスワード変更
    </span>

<div class="container mt-5">
    <!-- フラッシュメッセージの表示 -->
    @if (session('success'))
        <div id="success-message" class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="error-message" class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-center mb-4">予約履歴</h1>
    
    <hr>

    <div class="row">
        @foreach ($reservations as $reservation)
        <div class="col-md-4 col-sm-6 mb-4"> <!-- 1行に3列 (中画面以上), 2列 (小画面) -->
            <div class="card shadow-sm h-100">
                <!-- 店舗画像 -->
                @if (file_exists(public_path($reservation->product->image)))
                    <img src="{{ asset($reservation->product->image) }}" 
                        class="card-img-top" alt="{{ $reservation->product->name }}" style="height: 150px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/NOIMAGE.jpg') }}" 
                        class="card-img-top" alt="Default Image" style="height: 150px; object-fit: cover;">
                @endif

                <!-- 店舗情報 -->
                <div class="card-body" style="font-size: 14px;">
                    <h5 class="card-title">{{ $reservation->product->name }}</h5>
                    <p class="card-text">
                        <strong>予約日時:</strong> {{ $reservation->reservation_date }} {{ $reservation->reservation_time }}<br>
                        <strong>予約名:</strong> {{ $reservation->customer_name }}<br>
                        <strong>人数:</strong> {{ $reservation->people_count }}
                    </p>
                    
                    <!-- キャンセルボタン -->
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $reservation->id }}">
                        キャンセル
                    </button>

                    <!-- モーダル -->
                    <div class="modal fade" id="cancelModal-{{ $reservation->id }}" tabindex="-1" aria-labelledby="cancelModalLabel-{{ $reservation->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel-{{ $reservation->id }}">予約キャンセル確認</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    本当に予約をキャンセルしますか？
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                    <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">キャンセルする</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
