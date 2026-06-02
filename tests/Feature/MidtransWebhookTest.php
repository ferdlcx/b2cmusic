<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $address;
    private $category;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base data
        $this->user = User::create([
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->address = Address::create([
            'user_id' => $this->user->id,
            'label' => 'Rumah',
            'name' => 'Test Customer',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123',
            'city' => 'Jakarta Selatan',
            'city_id' => 153,
            'province' => 'DKI Jakarta',
            'province_id' => 6,
            'postal_code' => '12110',
            'is_default' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Guitars',
            'slug' => 'guitars',
            'description' => 'Test guitars category',
            'image' => 'https://example.com/guitar.jpg',
            'status' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Yamaha Pacifica 112V',
            'slug' => 'yamaha-pacifica-112v',
            'brand' => 'Yamaha',
            'short_description' => 'Test electric guitar',
            'description' => 'Detailed test electric guitar description',
            'price' => 3500000.00,
            'stock' => 10,
            'sku' => 'GTR-YAM-PAC112V',
            'weight' => 4000,
            'status' => true,
        ]);
    }

    public function test_webhook_successfully_updates_order_to_paid_on_settlement()
    {
        // 1. Create order and payment in pending state
        $orderCode = 'ORD-TEST-12345';
        $order = Order::create([
            'user_id' => $this->user->id,
            'shipping_address_id' => $this->address->id,
            'order_code' => $orderCode,
            'subtotal' => 3500000.00,
            'shipping_cost' => 25000.00,
            'discount' => 0.00,
            'total' => 3525000.00,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'va',
            'transaction_id' => 'TX-MOCK-123',
            'payment_gateway' => 'Midtrans',
            'snap_token' => 'mock-token',
            'amount' => 3525000.00,
            'status' => 'pending',
        ]);

        // 2. Prepare mock webhook payload
        $serverKey = config('services.midtrans.server_key') ?: 'dummy_server_key';
        config(['services.midtrans.server_key' => $serverKey]); // ensure key is set

        $statusCode = '200';
        $grossAmount = '3525000.00';
        $signatureKey = hash("sha512", $orderCode . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderCode,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-tx-id-999',
        ];

        // 3. Post to webhook endpoint
        $response = $this->postJson(route('midtrans.webhook'), $payload);

        // 4. Assert response and database status
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'transaction_id' => 'midtrans-tx-id-999',
        ]);
    }

    public function test_webhook_cancels_order_and_restores_stock_on_expiration()
    {
        // 1. Create order, item, and payment
        $orderCode = 'ORD-TEST-54321';
        $order = Order::create([
            'user_id' => $this->user->id,
            'shipping_address_id' => $this->address->id,
            'order_code' => $orderCode,
            'subtotal' => 7000000.00,
            'shipping_cost' => 25000.00,
            'discount' => 0.00,
            'total' => 7025000.00,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 3500000.00,
            'quantity' => 2,
            'subtotal' => 7000000.00,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'va',
            'transaction_id' => 'TX-MOCK-456',
            'payment_gateway' => 'Midtrans',
            'snap_token' => 'mock-token-2',
            'amount' => 7025000.00,
            'status' => 'pending',
        ]);

        // Manually decrease product stock to simulate purchased quantity (set from Setup's 10 - 2 = 8)
        $this->product->decrement('stock', 2);
        $this->assertEquals(8, $this->product->fresh()->stock);

        // 2. Prepare mock webhook payload with transaction_status = 'expire'
        $serverKey = config('services.midtrans.server_key') ?: 'dummy_server_key';
        config(['services.midtrans.server_key' => $serverKey]);

        $statusCode = '200';
        $grossAmount = '7025000.00';
        $signatureKey = hash("sha512", $orderCode . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderCode,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-tx-id-888',
        ];

        // 3. Post to webhook endpoint
        $response = $this->postJson(route('midtrans.webhook'), $payload);

        // 4. Assert response and database status
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Success']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'canceled',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed',
        ]);

        // Check that product stock is restored back to 10
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    public function test_webhook_returns_400_for_invalid_signature()
    {
        $orderCode = 'ORD-TEST-999';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $invalidSignature = 'invalid_signature_hash_here_12345';

        $payload = [
            'order_id' => $orderCode,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $invalidSignature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-tx-id-777',
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid signature']);
    }

    public function test_webhook_returns_404_for_unknown_order()
    {
        $serverKey = config('services.midtrans.server_key') ?: 'dummy_server_key';
        config(['services.midtrans.server_key' => $serverKey]);

        $orderCode = 'ORD-UNKNOWN-999';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $signatureKey = hash("sha512", $orderCode . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderCode,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-tx-id-777',
        ];

        $response = $this->postJson(route('midtrans.webhook'), $payload);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Order not found']);
    }
}
