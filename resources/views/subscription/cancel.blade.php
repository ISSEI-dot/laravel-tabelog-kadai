@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="text-danger">有料会員解約確認</h2>
    <p class="mt-3">本当に解約しますか？この操作は取り消せません。</p>

    <!-- 解約処理ボタン -->
    <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger">解約する</button>
        <a href="{{ route('mypage') }}" class="btn btn-secondary ms-3">キャンセル</a>
    </form>
</div>
@endsection
