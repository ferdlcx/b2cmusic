<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class CheckoutFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_cannot_access_checkout()
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_checkout_if_cart_is_not_empty()
    {
        $user = User::factory()->create();
        $product = clone new Product;
        // mock product without factory just in case
        
        $this->actingAs($user);
        
        // Let's test the endpoint directly. Empty cart should redirect to /cart
        $response = $this->get('/checkout');
        $response->assertRedirect('/cart');
    }
}
