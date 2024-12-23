<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyInfo;

class CompanyInfoController extends Controller
{
    public function index()
    {
        // データベースから1件目の会社情報を取得
        $companyInfo = CompanyInfo::first();

        // ビューにデータを渡して返す
        return view('company.index', compact('companyInfo'));
    }
}
