<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('courier'); // e.g. JNE, J&T, POS
            $table->string('service'); // e.g. REG, OKE, YES
            $table->string('tracking_number')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->enum('status', ['shipped', 'delivered', 'canceled', 'returned'])->default('shipped');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
