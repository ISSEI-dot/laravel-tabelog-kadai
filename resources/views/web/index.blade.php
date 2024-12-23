@extends('layouts.app')
 
 @section('content')
 <div class="row">
     <div class="col-2">
     @component('components.sidebar', ['categories' => $categories, 'major_categories' => $major_categories])
         @endcomponent
     </div>
     <div class="col-9">
         <h1>おすすめメニュー<a href="{{ url('/products') }}" class="btn-more">もっと見る</a></h1>
         <div class="row">
         @foreach ($recommend_products as $recommend_product)
             <div class="col-4">
                 <a href="{{ route('products.show', $recommend_product) }}">
                     @if ($recommend_product->image !== "")
                     <img src="{{ asset($recommend_product->image) }}" class="img-thumbnail">
                     @else
                     <img src="{{ asset('img/dummy.png')}}" class="img-thumbnail">
                     @endif
                 </a>
                 <div class="row">
                     <div class="col-12">
                         <p class="kadai_002-product-label mt-2">
                             {{ $recommend_product->name }}<br>
                             @if ($recommend_product->reviews()->exists())
                                     <span class="kadai_002-star-rating" data-rate="{{ round($recommend_product->reviews->avg('score') * 2) / 2 }}"></span>
                                     {{ round($recommend_product->reviews->avg('score'), 1) }}<br>
                                 @endif
                             <label>価格帯：￥{{ $recommend_product->price }}～￥{{ $recommend_product->price_max }}</label>
                         </p>
                     </div>
                 </div>
             </div>
             @endforeach
 
         </div>
 
         <h1>新着メニュー<a href="{{ url('/products') }}" class="btn-more">もっと見る</a></h1>
         <div class="row">
         @foreach ($recently_products as $recently_product)
             <div class="col-3">
                 <a href="{{ route('products.show', $recently_product) }}">
                     @if ($recently_product->image !== "")
                     <img src="{{ asset($recently_product->image) }}" class="img-thumbnail">
                     @else
                     <img src="{{ asset('img/dummy.png')}}" class="img-thumbnail">
                     @endif
                 </a>
                 <div class="row">
                     <div class="col-12">
                         <p class="kadai_002-product-label mt-2">
                             {{ $recently_product->name }}<br>
                             @if ($recently_product->reviews()->exists())
                                 <span class="kadai_002-star-rating" data-rate="{{ round($recently_product->reviews->avg('score') * 2) / 2 }}"></span>
                                 {{ round($recently_product->reviews->avg('score'), 1) }}<br>
                             @endif
                             <label>￥{{ $recently_product->price }}～</label>
                         </p>
                     </div>
                 </div>
             </div>
         @endforeach
         </div>
     </div>
 </div>
 @endsection