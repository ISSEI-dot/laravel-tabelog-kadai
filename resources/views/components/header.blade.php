<nav class="navbar navbar-expand-md navbar-light shadow-sm kadai_002-header-container">
   <div class="container">
     <a class="navbar-brand" href="{{ url('/') }}">
     <img src="{{asset('img/logo.png')}}"> NAGOYAMESHI
     </a>
     <form action="{{ route('products.index') }}" method="GET" class="row g-1">
       <div class="col-auto">
         <input class="form-control kadai_002-header-search-input" name="keyword">
       </div>
       <div class="col-auto">
         <button type="submit" class="btn kadai_002-header-search-button"><i class="fas fa-search kadai_002-header-search-icon"><strong>店舗名を入力</strong></i></button>
       </div>
     </form>
     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
       <span class="navbar-toggler-icon"></span>
     </button>
 
     <div class="collapse navbar-collapse" id="navbarSupportedContent">
       <!-- Right Side Of Navbar -->
       <ul class="navbar-nav ms-auto mr-5 mt-2">
         <!-- Authentication Links -->
         @guest
         <li class="nav-item mr-5">
         <a href="{{ route('register') }}" class="nav-link nav-item-link primary">新規登録</a>
         </li>
         <li class="nav-item mr-5">
         <a href="{{ route('login') }}" class="nav-link nav-item-link primary">ログイン</a>
         </li>
         <hr>
         @else
         <li class="nav-item mr-5">
           <a class="nav-link nav-item-link mypage-link" href="{{ route('mypage') }}">
             <i class="fas fa-user mr-1"></i>マイページ
           </a>
         </li>
         @if(Auth::user()->subscribed('default')) <!-- サブスク登録者のみ表示 -->
         <li class="nav-item mr-5">
           <a class="nav-link nav-item-link favorite-link" href="{{ route('mypage.favorite') }}">
             <i class="far fa-heart"></i>お気に入り
           </a>
         </li>
         @else
         <!-- サブスク未登録者向けメッセージ -->
         <li class="nav-item mr-5">
           <span class="nav-link text-danger">お気に入りは有料プランのみ</span>
         </li>
         @endif
         
         @endguest

        <li class="nav-item mr-5">
          <a class="nav-link nav-item-link company-link" href="{{ route('company.index') }}">
            <i class="fas fa-building"></i>会社情報
          </a>
        </li>
       </ul>
     </div>
   </div>
 </nav>