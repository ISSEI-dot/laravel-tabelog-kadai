@extends('layouts.app')

@section('content')

<div class="container">
    <span>
        <a href="{{ route('mypage') }}">マイページ</a> > お気に入り
    </span>

<div class="container d-flex justify-content-center mt-3">
    <div class="w-75">
        <h1>お気に入り</h1>

        <hr>

        <div class="row">
            @foreach ($favorites as $fav)
            <div class="col-md-7 mt-2">
                <div class="d-inline-flex">
                    <a href="{{ route('products.show', $fav->favoriteable_id) }}" class="w-25">
                    @if (App\Models\Product::find($fav->favoriteable_id)->image !== "")
                        <img src="{{ asset(App\Models\Product::find($fav->favoriteable_id)->image) }}" class="img-fluid w-100">
                    @else
                        <img src="{{ asset('img/dummy.png') }}" class="img-fluid w-100">
                    @endif
                    </a>
                    <div class="container mt-3">
                        <h5 class="w-100 kadai_002-favorite-item-text">{{ App\Models\Product::find($fav->favoriteable_id)->name }}</h5>
                        <h6 class="w-100 kadai_002-favorite-item-text">&yen;{{ App\Models\Product::find($fav->favoriteable_id)->price }}～</h6>
                    </div>
                </div>
            </div>

            <!-- お気に入り解除ボタン -->
            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <form method="POST" action="{{ route('products.favorite', $fav->favoriteable_id) }}">
                    @csrf
                    <button type="submit" class="btn kadai_002-favorite-item-delete text-favorite w-100">
                        解除
                    </button>
                </form>
            </div>

            <!-- 予約ボタン -->
            <div class="col-md-3 d-flex align-items-center justify-content-end">
                @if(Auth::user()->subscribed('default'))
                    <a href="{{ route('reservations.create', ['product' => $fav->favoriteable_id]) }}" class="btn kadai_002-reserve-button text-reserve w-100">
                        予約する
                    </a>
                @else
                    <!-- サブスク未登録者向けメッセージ -->
                    <p class="alert alert-warning">予約機能を利用するには有料プランに登録してください。</p>
                @endif
            </div>
            @endforeach
        </div>

        <hr>
    </div>
</div>
@endsection
