@extends('layouts.app')

@section('content')
<div class="container">
    <h1>レビューの編集</h1>
    <form method="POST" action="{{ route('reviews.update', $review) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="score">評価</label>
            <select name="score" id="score" class="form-control">
                <option value="5" {{ $review->score == 5 ? 'selected' : '' }}>★★★★★</option>
                <option value="4" {{ $review->score == 4 ? 'selected' : '' }}>★★★★</option>
                <option value="3" {{ $review->score == 3 ? 'selected' : '' }}>★★★</option>
                <option value="2" {{ $review->score == 2 ? 'selected' : '' }}>★★</option>
                <option value="1" {{ $review->score == 1 ? 'selected' : '' }}>★</option>
            </select>
        </div>

        <div class="form-group">
            <label for="content">レビュー内容</label>
            <textarea name="content" id="content" class="form-control">{{ $review->content }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
