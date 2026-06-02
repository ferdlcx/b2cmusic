<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label'); // e.g. Rumah, Kantor
            $table->string('name');  // Recipient name
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->integer('city_id')->nullable();
            $table->string('province');
            $table->integer('province_id')->nullable();
            $table->string('postal_code');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
