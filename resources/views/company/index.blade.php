@extends('layouts.app')

@section('content')
<div class="company-page">
    <div class="company-info">
        <h1>会社情報</h1>

        <section>
            <h3>会社概要</h3>
            <p>会社名: {{ $companyInfo->company_name }}</p>
            <p>郵便番号: {{ $companyInfo->postal_code }}</p>
            <p>所在地: {{ $companyInfo->address }}</p>
            <p>設立: {{ $companyInfo->established_date }}</p>
            <p>代表者: {{ $companyInfo->representative }}</p>
        </section>

        <section>
            <h3>事業内容</h3>
            <p>{!! nl2br(e($companyInfo->business_content)) !!}</p>
        </section>

        <section>
            <h3>お問い合わせ</h3>
            <p>Email: <a href="mailto:{{ $companyInfo->email }}">{{ $companyInfo->email }}</a></p>
            <p>電話: {{ $companyInfo->phone_number }}</p>
        </section>
    </div>
</div>
@endsection
