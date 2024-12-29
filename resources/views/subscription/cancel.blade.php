@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <h2 class="text-center">サブスクリプション解約</h2>
    <p class="text-center">サブスクリプションは正常に解約されました。</p>
    <div class="text-center mt-4">
        <a href="{{ route('top') }}" class="btn btn-primary">トップに戻る</a>
    </div>
</div>
@endsection
