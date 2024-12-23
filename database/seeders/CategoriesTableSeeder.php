<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $major_category_names = [
            '和食', '洋食', '中華'
         ];

         $japanese_food_categories = [
            '寿司','天ぷら','うどん・そば','定食','丼物'
         ];

         $western_food_categories = [
            'パスタ','ピザ','グラタン・ドリア','ステーキ・ハンバーグ','サンドイッチ・ハンバーガー'
         ];

         $chinese_food_categories = [
            'ラーメン','チャーハン','点心（餃子・シュウマイ）','麻婆料理','中華丼'
         ];

         foreach ($major_category_names as $major_category_name) {
            if ($major_category_name == '和食') {
                foreach ($japanese_food_categories as $japanese_food_category) {
                    Category::create([
                        'name' => $japanese_food_category,
                        'description' => $japanese_food_category,
                        'major_category_name' => $major_category_name
                    ]);
                }
            }

            if ($major_category_name == '洋食') {
                foreach ($western_food_categories as $western_food_category) {
                    Category::create([
                        'name' => $western_food_category,
                        'description' => $western_food_category,
                        'major_category_name' => $major_category_name
                    ]);
                }
            }

            if ($major_category_name == '中華') {
                foreach ($chinese_food_categories as $chinese_food_category) {
                    Category::create([
                        'name' => $chinese_food_category,
                        'description' => $chinese_food_category,
                        'major_category_name' => $major_category_name
                    ]);
                }
            }
        }
    }
}
