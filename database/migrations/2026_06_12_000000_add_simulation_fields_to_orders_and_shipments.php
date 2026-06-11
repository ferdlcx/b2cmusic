<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_simulation')->default(false)->after('waybill_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_simulation');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->enum('status', ['shipped', 'delivered', 'canceled', 'returned'])->default('shipped')->change();
        });
    }
};
