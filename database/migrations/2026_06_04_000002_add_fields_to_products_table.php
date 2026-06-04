<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')
                ->constrained('brands')->onDelete('set null');
            $table->enum('condition', ['new', 'used', 'refurbished'])->default('new')->after('status');
            $table->unsignedInteger('sold_count')->default(0)->after('condition');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'condition', 'sold_count', 'deleted_at']);
        });
    }
};
