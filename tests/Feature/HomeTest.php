<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_guest_can_access_home_page()
    {
        // 未ログインユーザーとしてトップページにアクセス
        $response = $this->get('/');

        // ステータスコード200を確認
        $response->assertStatus(200);
    }
}
