@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h3>予約フォーム</h3>
                </div>

                <div class="card-body">

                    <!-- エラーメッセージの表示 -->
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- エラーメッセージの表示 -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                <!-- 予約フォーム -->
                <div class="card-body">
                    <form action="{{ route('reservations.store', ['product' => $product->id]) }}" method="POST">
                        @csrf
                        <!-- 店舗名表示 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">店舗名</label>
                            <p class="form-control-plaintext">{{ $product->name }}</p>
                        </div>

                        <!-- 名前入力 -->
                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-bold">お名前（必須）</label>
                            <input type="text" id="customer_name" name="customer_name" 
                                   class="form-control" placeholder="お名前を入力してください" required>
                        </div>

                        <!-- 人数入力 -->
                        <div class="mb-3">
                            <label for="people_count" class="form-label fw-bold">人数（必須）</label>
                            <input type="number" id="people_count" name="people_count" 
                                   class="form-control" min="1" placeholder="例: 2" required>
                        </div>

                        <!-- 予約日入力 -->
                        <div class="mb-3">
                            <label for="reservation_date" class="form-label fw-bold">日付（必須）</label>
                            <input type="date" id="reservation_date" name="reservation_date" 
                                   class="form-control" min="" required>
                        </div>

                        <!-- 予約時間（営業時間内のみ） -->
                        <div class="mb-4">
                            <label for="reservation_time" class="form-label fw-bold">予約時間（必須）</label>
                            <select id="reservation_time" name="reservation_time" class="form-select" required>
                                <option value="" disabled selected>予約する時間を選択してください</option>
                                @php
                                    use Carbon\Carbon;
                        
                                    // 開店・閉店時間をCarbonオブジェクトとして取得
                                    $openingTime = Carbon::createFromFormat('H:i:s', $product->opening_time);
                                    $closingTime = Carbon::createFromFormat('H:i:s', $product->closing_time);
                                @endphp
                                
                                @if ($openingTime && $closingTime && $openingTime->lt($closingTime))
                                    <!-- 営業時間内で1時間ごとに選択肢を生成 -->
                                    @while ($openingTime->lte($closingTime))
                                        <option value="{{ $openingTime->format('H:i') }}">
                                            {{ $openingTime->format('H:i') }}
                                        </option>
                                        @php
                                            $openingTime->addHour(); // 1時間刻みで追加
                                        @endphp
                                    @endwhile
                                @else
                                    <option disabled>営業時間が設定されていません</option>
                                @endif
                            </select>
                        </div>
                        
                        

                        <!-- 送信ボタン -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-success w-50">
                                予約する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 現在の日付を取得して3日後と半年後の日付を計算
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        
        // 3日後の設定
        const threeDaysLater = new Date();
        threeDaysLater.setDate(today.getDate() + 3);
        
        // 半年後の設定
        const sixMonthsLater = new Date();
        sixMonthsLater.setMonth(today.getMonth() + 6);

        // yyyy-mm-dd 形式にフォーマット
        const minDate = threeDaysLater.toISOString().split('T')[0];
        const maxDate = sixMonthsLater.toISOString().split('T')[0];

        // 定休日（曜日）を取得
        const regularHolidays = @json($regular_holidays);

        // input 要素に min と max を設定
        const reservationDateInput = document.getElementById('reservation_date');
        reservationDateInput.setAttribute('min', minDate);
        reservationDateInput.setAttribute('max', maxDate);

        // 日付が選択されたときに定休日をチェック
        reservationDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const selectedDay = selectedDate.toLocaleString('en-US', { weekday: 'long' });

            if (regularHolidays.includes(selectedDay)) {
                alert('選択した日は定休日です。他の日を選択してください。');
                this.value = ''; // 入力をクリア
            }
        });
    });
</script>
@endsection
