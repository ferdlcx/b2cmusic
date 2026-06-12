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
            'price' => 5000000,
            'stock' => 10,
            'weight' => 3000,
            'description' => 'Gitar akustik premium.',
            'is_active' => true,
        ]);

        // 2. Simulasi akses halaman katalog
        $response = $this->get('/katalog');

        // 3. Verifikasi
        $response->assertStatus(200);
        $response->assertSee('Fender Stratocaster');
        $response->assertSee('Rp 5.000.000');
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

        // 3. Verifikasi response sukses
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // 4. Verifikasi data tersimpan di database keranjang
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
    }
}
