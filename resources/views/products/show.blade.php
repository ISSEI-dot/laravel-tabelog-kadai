@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center">
    <div class="row w-75">
        <div class="col-5 offset-1">
            @if ($product->image)
                <img src="{{ asset($product->image) }}" class="w-100 img-fluid">
            @else
                <img src="{{ asset('img/dummy.png')}}" class="w-100 img-fluid">
            @endif
        </div>
        <div class="col">
            <div class="d-flex flex-column">
                <h1 class="">
                    {{$product->name}}
                </h1>
                @if ($product->reviews()->exists())
                    <p>
                        <span class="kadai_002-star-rating" data-rate="{{ round($product->reviews->avg('score') * 2) / 2 }}"></span>
                        {{ round($product->reviews->avg('score'), 1) }}
                    </p>
                @endif
                <p class="">
                   {{$product->description}}
                </p>
                <hr>
                <p class="d-flex align-items-end">価格帯:
                   ￥{{ $product->price }} ～ ￥{{ $product->price_max }}
                </p>
                <hr>
                <p class="">営業時間: {{ $product->opening_time }} ～ {{ $product->closing_time }}</p>
                <hr>
                <p class="">定休日: {{ $product->regular_holiday }}</p>
                <hr>
                <p class="">郵便番号: {{ $product->postal_code }}</p>
                <hr>
                <p class="">住所: {{ $product->address }}</p>
                <hr>
                <p class="">電話番号: {{ $product->phone_number }}</p>
            </div>

            @auth
            <div class="d-flex justify-content-start mt-3 gap-2"> <!-- 横並び配置 -->
                <!-- 予約機能 -->
                @if(Auth::user()->subscribed('default'))
                    <a href="{{ route('reservations.create', ['product' => $product->id]) }}" class="btn kadai_002-reserve-button text-reserve">
                        <i class="fa fa-calendar-days"></i>
                        予約する
                    </a>
                @else
                    <p class="alert alert-warning">※予約機能は、有料会員に登録する必要があります。</p>
                @endif

                <!-- お気に入り機能 -->
                @if(Auth::user()->subscribed('default'))
                <form method="POST" action="{{ route('products.favorite', $product->id) }}">
                    @csrf
                    <button type="submit" class="btn kadai_002-favorite-button text-favorite">
                        @if($product->isFavoritedBy(Auth::user()))
                            <i class="fa fa-heart"></i> お気に入り解除
                        @else
                            <i class="fa fa-heart"></i> お気に入り
                        @endif
                    </button>
                </form>
                @else
                    <p class="alert alert-warning">※お気に入り機能は、有料会員に登録する必要があります。</p>
                @endif
            </div>
            @endauth
        </div>

        <div class="offset-1 col-11">
            <hr class="w-100">
            <h3 class="float-left">カスタマーレビュー</h3>
            @if ($product->reviews()->exists())
                <p>
                    <span class="kadai_002-star-rating" data-rate="{{ round($product->reviews->avg('score') * 2) / 2 }}"></span>
                    {{ round($product->reviews->avg('score'), 1) }}
                </p>
            @endif
        </div>

        <div class="offset-1 col-10">
        <div class="row">
               @foreach($reviews as $review)
                <div class="offset-md-5 col-md-5">
                    <h3 class="review-score-color">{{ str_repeat('★', $review->score) }}</h3>
                    <p class="h3">{{$review->content}}</p>
                    <label>{{$review->created_at}} {{$review->user->name}}</label>

                    @auth
                       @if(Auth::id() === $review->user_id && Auth::user()->subscribed('default'))
                       <div class="d-flex justify-content-start align-items-center mt-2">
                           <a href="{{ route('reviews.edit', $review) }}" class="btn btn-warning">編集</a>
                           <form method="POST" action="{{ route('reviews.destroy', $review) }}" style="display:inline;">
                               @csrf
                               @method('DELETE')
                               <button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                           </form>
                       </div>
                       @endif
                    @endauth
                </div>
               @endforeach
            </div><br />

            @auth
            @if(Auth::user()->subscribed('default'))
            <div class="row">
                <div class="offset-md-5 col-md-5">
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
                        <h4>評価</h4>
                        <select name="score" class="form-control m-2 review-score-color">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                        <h4>レビュー内容</h4>
                        @error('content')
                            <strong>レビュー内容を入力してください</strong>
                        @enderror
                        <textarea name="content" class="form-control m-2"></textarea>
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <button type="submit" class="btn kadai_002-submit-button ml-2 text-submit w-100">レビューを追加</button>
                    </form>
                </div>
            </div>
            @else
                <p class="alert alert-warning">※レビューを投稿するには、有料会員に登録する必要があります。</p>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection
