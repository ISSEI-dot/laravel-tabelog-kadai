@extends('layouts.app')
 
@section('content')
 <div class="container d-flex justify-content-center mt-3">
     <div class="w-50">
         <h1>マイページ</h1>

         <!-- エラーメッセージの表示 -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
 
         <hr>
 
         <div class="container">
             <div class="d-flex justify-content-between">
                 <div class="row">
                     <div class="col-2 d-flex align-items-center">
                         <i class="fas fa-user fa-3x"></i>
                     </div>
                     <div class="col-9 d-flex align-items-center ms-2 mt-3">
                         <div class="d-flex flex-column">
                             <label for="user-name">会員情報の編集</label>
                             <p>アカウント情報の編集</p>
                         </div>
                     </div>
                 </div>
                 <div class="d-flex align-items-center">
                     <a href="{{route('mypage.edit')}}">
                         <i class="fas fa-chevron-right fa-2x"></i>
                     </a>
                 </div>
             </div>
         </div>
 
         <hr>
         <!-- サブスク未登録者のみ表示 -->
         @if(!Auth::user()->subscribed('default'))

         <!-- 有料会員登録 -->
        <div class="container">
            <div class="d-flex justify-content-between">
                <div class="row">
                    <div class="col-2 d-flex align-items-center">
                        <i class="fas fa-gem fa-3x"></i>
                    </div>
                    <div class="col-9 d-flex align-items-center ms-2 mt-3">
                        <div class="d-flex flex-column">
                            <label for="premium-plan">有料会員登録</label>
                            <p>有料会員に登録します</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('subscription.index') }}">
                        <i class="fas fa-chevron-right fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr>

        @endif

        <!-- サブスク登録者のみ表示 -->
        @if(Auth::user()->subscribed('default'))

        <!-- お支払い方法編集 -->
        <div class="container">
            <div class="d-flex justify-content-between">
                <div class="row">
                    <div class="col-2 d-flex align-items-center">
                        <i class="fas fa-credit-card fa-3x"></i>
                    </div>
                    <div class="col-9 d-flex align-items-center ms-2 mt-3">
                        <div class="d-flex flex-column">
                            <label for="payment-method">お支払い方法編集</label>
                            <p>お支払い方法を編集します</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('subscription.edit') }}">
                        <i class="fas fa-chevron-right fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr>

        <!-- 有料会員解約 -->
        <div class="container">
            <div class="d-flex justify-content-between">
                <div class="row">
                    <div class="col-2 d-flex align-items-center">
                        <i class="fas fa-times-circle fa-3x"></i>
                    </div>
                    <div class="col-9 d-flex align-items-center ms-2 mt-3">
                        <div class="d-flex flex-column">
                            <label for="cancel-subscription">有料会員解約</label>
                        <p>有料会員を解約します</p>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <!-- 解約確認ページへ遷移 -->
                <a href="{{ route('subscription.cancel.view') }}">
                    <i class="fas fa-chevron-right fa-2x"></i>
                </a>
            </div>
        </div>
    </div>

        <hr>
 
         <div class="container">
             <div class="d-flex justify-content-between">
                 <div class="row">
                     <div class="col-2 d-flex align-items-center">
                         <i class="fas fa-archive fa-3x"></i>
                     </div>
                     <div class="col-9 d-flex align-items-center ms-2 mt-3">
                         <div class="d-flex flex-column">
                             <label for="user-name">予約履歴</label>
                             <p>予約履歴を確認できます</p>
                         </div>
                     </div>
                 </div>
                 <div class="d-flex align-items-center">
                 <a href="{{ route('reservations.index') }}">
                         <i class="fas fa-chevron-right fa-2x"></i>
                     </a>
                 </div>
             </div>
         </div>

         <hr>

         <div class="container">
            <div class="d-flex justify-content-between">
                <div class="row">
                    <div class="col-2 d-flex align-items-center">
                        <i class="fas fa-heart fa-3x"></i>
                    </div>
                    <div class="col-9 d-flex align-items-center ms-2 mt-3">
                        <div class="d-flex flex-column">
                            <label for="user-name">お気に入り一覧</label>
                            <p>お気に入りを確認できます</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('favorites.index') }}">
                        <i class="fas fa-chevron-right fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
 
         <hr>

         @endif

         <div class="container">
             <div class="d-flex justify-content-between">
                 <div class="row">
                     <div class="col-2 d-flex align-items-center">
                         <i class="fas fa-lock fa-3x"></i>
                     </div>
                     <div class="col-9 d-flex align-items-center ms-2 mt-3">
                         <div class="d-flex flex-column">
                             <label for="user-name">パスワード変更</label>
                             <p>パスワードを変更します</p>
                         </div>
                     </div>
                 </div>
                 <div class="d-flex align-items-center">
                     <a href="{{ route('mypage.edit_password') }}">
                         <i class="fas fa-chevron-right fa-2x"></i>
                     </a>
                 </div>
             </div>
         </div>
 
         <hr>
 
         <div class="container">
             <div class="d-flex justify-content-between">
                 <div class="row">
                     <div class="col-2 d-flex align-items-center">
                         <i class="fas fa-sign-out-alt fa-3x"></i>
                     </div>
                     <div class="col-9 d-flex align-items-center ms-2 mt-3">
                         <div class="d-flex flex-column">
                             <label for="user-name">ログアウト</label>
                             <p>ログアウトします</p>
                         </div>
                     </div>
                 </div>
                 <div class="d-flex align-items-center">
                     <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                         <i class="fas fa-chevron-right fa-2x"></i>
                     </a>
 
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                         @csrf
                     </form>
                 </div>
             </div>
         </div>
 
         <hr>
     </div>
 </div>
 @endsection