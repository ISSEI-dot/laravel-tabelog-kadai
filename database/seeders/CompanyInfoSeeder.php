<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanyInfo;

class CompanyInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompanyInfo::create([
            'company_name' => '株式会社NAGOYAMESHI',
            'address' => '東京都渋谷区1-2-3',
            'established_date' => '2022-01-01',
            'representative' => '山田 太郎',
            'business_content' => 'Webアプリの開発\nITコンサルティング',
            'email' => 'info@company.com',
            'phone_number' => '03-1234-5678',
        ]);
    }
}
