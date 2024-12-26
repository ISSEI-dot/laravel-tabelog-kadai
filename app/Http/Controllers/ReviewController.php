<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $review = new Review();
        $review->content = $request->input('content');
        $review->product_id = $request->input('product_id');
        $review->user_id = Auth::user()->id;
        $review->score = $request->input('score');
        $review->save();

        return back();
    }

    public function edit(Review $review)
    {
        // ログインユーザーがレビューの作成者でなければ403エラーを返す
        if (Auth::id() !== $review->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        // ログインユーザーがレビューの作成者でなければ403エラーを返す
        if (Auth::id() !== $review->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:255',
        ]);

        $review->update([
            'score' => $request->score,
            'content' => $request->content,
        ]);

        return redirect()->route('products.show', $review->product_id)->with('success', 'レビューを更新しました！');
    }

    public function destroy(Review $review)
    {
        // ログインユーザーがレビューの作成者でなければ403エラーを返す
        if (Auth::id() !== $review->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return redirect()->route('products.show', $review->product_id)->with('success', 'レビューを削除しました！');
    }
}