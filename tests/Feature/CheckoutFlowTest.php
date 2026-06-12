<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase; // Memastikan database kosong setiap kali test dijalankan

    public function test_user_can_view_catalog()
    {
        // 1. Buat data dummy
        $category = Category::create([
            'name' => 'Gitar',
            'slug' => 'gitar',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fender Stratocaster',
            'slug' => 'fender-stratocaster',
            'sku' => 'GTR-FEN-01',
            'price' => 5000000,
            'stock' => 10,
            'weight' => 3000,
            'description' => 'Gitar akustik premium.',
            'is_active' => true,
        ]);

        // 2. Simulasi akses halaman katalog
        $response = $this->get('/catalog');

        // 3. Verifikasi
        $response->assertStatus(200);
        $response->assertSee('Fender Stratocaster');
        $response->assertSee('5.000.000');
    }

    public function test_user_can_add_product_to_cart()
    {
        // 1. Setup user & product
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Drum', 'slug' => 'drum']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Yamaha Drum Set',
            'slug' => 'yamaha-drum',
            'sku' => 'DRM-YMH-01',
            'price' => 12000000,
            'stock' => 5,
            'weight' => 25000,
            'is_active' => true,
        ]);

        // 2. Simulasi login dan tambah keranjang
        $response = $this->actingAs($user)
                         ->postJson('/cart/add', [
                             'product_id' => $product->id,
                             'quantity' => 1
                         ]);

        // 3. Verifikasi response (biasanya 302 Redirect jika via web route, atau 200 jika API)
        if ($response->status() == 302) {
            $response->assertStatus(302);
        } else {
            $response->assertStatus(200);
            $response->assertJson(['success' => true]);
        }

        // 4. Verifikasi data tersimpan di database keranjang
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
    }
}
