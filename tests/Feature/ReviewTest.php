<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Laravel\Cashier\Exceptions\IncompletePayment;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedUser()
    {

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiVersion('2023-08-16');

        $user = User::factory()->create();
        $user->createAsStripeCustomer();

        // モック支払い方法を追加
    $paymentMethod = $user->addPaymentMethod('pm_card_visa');

    // サブスクリプション作成時の処理
    $user->newSubscription('default', 'price_1QbxEzBUjnqExYiQ5Ra2tpaC')
         ->create($paymentMethod->id); // 支払い方法IDを指定

        return $user;
    }

    /** @test */
    public function subscribed_user_can_create_review()
    {
        $user = $this->createSubscribedUser();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/reviews', [
            'content' => 'これはテストレビューです。',
            'product_id' => $product->id,
            'score' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'content' => 'これはテストレビューです。',
            'product_id' => $product->id,
            'score' => 5,
        ]);
    }

    /** @test */
    public function non_subscribed_user_cannot_create_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/reviews', [
            'content' => 'これはテストレビューです。',
            'product_id' => $product->id,
            'score' => 5,
        ]);

        $response->assertRedirect(route('mypage'));
        $this->assertDatabaseMissing('reviews', [
            'content' => 'これはテストレビューです。',
        ]);
    }

    /** @test */
    public function subscribed_user_can_edit_their_review()
    {
        $user = $this->createSubscribedUser();
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->put("/reviews/{$review->id}", [
            'content' => '編集後のレビュー',
            'score' => 4,
        ]);

        $response->assertRedirect(route('products.show', $product->id));
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'content' => '編集後のレビュー',
            'score' => 4,
        ]);
    }


    /** @test */
    public function non_owner_cannot_edit_review()
    {
        $user = $this->createSubscribedUser();
        $otherUser = $this->createSubscribedUser();
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($otherUser)->put("/reviews/{$review->id}", [
            'content' => '編集後のレビュー',
            'score' => 4,
        ]);

        $response->assertStatus(403);
    }


    /** @test */
    public function user_can_delete_their_review()
    {
        $user = $this->createSubscribedUser();
        $product = Product::factory()->create();
        $review = Review::factory()->create([ 'user_id' => $user->id, 'product_id' => $product->id ]);

        $response = $this->actingAs($user)->delete("/reviews/{$review->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('reviews', [ 'id' => $review->id ]);
    }

    /** @test */
    public function non_owner_cannot_delete_review()
    {
        $user = $this->createSubscribedUser();
        $otherUser = $this->createSubscribedUser();
        $product = Product::factory()->create();
        $review = Review::factory()->create([ 'user_id' => $user->id, 'product_id' => $product->id ]);

        $response = $this->actingAs($otherUser)->delete("/reviews/{$review->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', [ 'id' => $review->id ]);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $user = $this->createSubscribedUser();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/reviews', [
            'content' => '', // 空のコンテンツ
            'product_id' => $product->id,
            'score' => 6, // 無効なスコア
        ]);

        $response->assertSessionHasErrors(['content', 'score']);
    }
}
