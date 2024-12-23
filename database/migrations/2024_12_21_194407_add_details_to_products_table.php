<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('price_max')->nullable()->after('price'); // 上限価格
        $table->string('regular_holiday')->nullable()->after('price_max'); // 定休日
        $table->time('opening_time')->nullable()->after('regular_holiday'); // 開店時間
        $table->time('closing_time')->nullable()->after('opening_time'); // 閉店時間
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
