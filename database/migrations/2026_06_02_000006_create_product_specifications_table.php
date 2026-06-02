<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('spec_name');
            $table->string('spec_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
