<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function index()
    {
        // ユーザーのお気に入りを取得
        $favorites = auth()->user()->favorites;

        // お気に入り一覧ページを表示
        return view('users.favorite', compact('favorites'));
    }
}
