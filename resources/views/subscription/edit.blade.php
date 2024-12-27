@extends('layouts.app')

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripeKey = "{{ config('services.stripe.key') }}";
    </script>
    <script src="{{ asset('/js/stripe.js') }}"></script>
@endpush

@section('content')
    <div class="container kadai_002-container pb-5">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <nav class="my-3" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('mypage') }}">マイページ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">お支払い方法</li>

                    </ol>
                </nav>

                <h1 class="mb-3 text-center" id="test">お支払い方法</h1>

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <hr>

                <div class="container mb-4">
                    <div class="row pb-2 mb-2 border-bottom">
                        <div class="col-3">
                            <span class="fw-bold">カード種別</span>
                        </div>

                        <div class="col">
                            <span>{{ $user->pm_type }}</span>
                        </div>
                    </div>

                    <div class="row pb-2 mb-2 border-bottom">
                        <div class="col-3">
                            <span class="fw-bold">カード名義人</span>
                        </div>

                        <div class="col">
                            <span>
                                @if ($user->defaultPaymentMethod())
                                    {{ $user->defaultPaymentMethod()->billing_details->name  }}
                                @else
                                    登録なし
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="row pb-2 mb-2 border-bottom">
                        <div class="col-3">
                            <span class="fw-bold">カード番号</span>
                        </div>

                        <div class="col">
                            <span>
                                @if ($user->pm_last_four)
                                    **** **** **** {{ $user->pm_last_four }}
                                @else
                                    なし
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger kadai_002-card-error" id="card-error" role="alert" style="display: none;">
                    <ul class="mb-0" id="error-list"></ul>
                </div>

                <form id="card-form" action="{{ route('subscription.update') }}" method="post">
                    @csrf
                    <input class="form-control mb-3" id="card-holder-name" type="text" placeholder="カード名義人" required>
                    <div class="kadai_002-card-element mb-4" id="card-element"></div>
                    <div class="d-flex justify-content-center">
                        <button class="btn text-black shadow-sm w-50 kadai_002-btn" id="card-button" data-secret="{{ $intent->client_secret }}">変更</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection