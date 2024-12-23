@extends('layouts.app')
 
 @section('content')
 <div class="row">
     <div class="col-2">
         @component('components.sidebar', ['categories' => $categories, 'major_categories' => $major_categories])
         @endcomponent
     </div>
     <div class="col-9">
         <div class="container">
             @if ($category !== null)
                 <a href="{{ route('products.index') }}">トップ</a> > <a href="#">{{ $category->major_category_name }}</a> > {{ $category->name }}
                 <h1>{{ $category->name }}の店舗一覧{{$total_count}}件</h1>
                 @elseif ($keyword !== null)
                 <a href="{{ route('products.index') }}">トップ</a> > 店舗一覧
                 <h1>"{{ $keyword }}"の検索結果{{$total_count}}件</h1>
             @endif
         </div>
         <div>
             Sort By
             @sortablelink('id', 'ID')
             @sortablelink('price', 'Price')
         </div>
         <div class="container mt-4">
             <div class="row w-100">
                 @foreach($products as $product)
                 <div class="col-3">
                     <a href="{{route('products.show', $product)}}">
                     @if ($product->image !== "")
                         <img src="{{ asset($product->image) }}" class="img-thumbnail">
                         @else
                         <img src="{{ asset('img/dummy.png')}}" class="img-thumbnail">
                         @endif
                     </a>
                     <div class="row">
                         <div class="col-12">
                             <p class="kadai_002-product-label mt-2">
                                 {{$product->name}}<br>
                                 @if ($product->reviews()->exists())
                                     <span class="kadai_002-star-rating" data-rate="{{ round($product->reviews->avg('score') * 2) / 2 }}"></span>
                                     {{ round($product->reviews->avg('score'), 1) }}<br>
                                 @endif
                                 <label>価格帯:
                                 ￥{{ $product->price }} ～ ￥{{ $product->price_max }}</label>
                             </p>
                         </div>
                     </div>
                 </div>
                 @endforeach
             </div>
         </div>
         {{$products->appends(request()->query())->links() }}
     </div>
 </div>
 @endsection