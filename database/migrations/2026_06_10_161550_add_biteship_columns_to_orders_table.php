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
            $table->string('courier_company')->nullable()->after('status');
            $table->string('courier_type')->nullable()->after('courier_company');
            $table->string('biteship_order_id')->nullable()->after('courier_type');
            $table->string('waybill_id')->nullable()->after('biteship_order_id');
            $table->string('tracking_id')->nullable()->after('waybill_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_company', 'courier_type', 'biteship_order_id', 'waybill_id', 'tracking_id']);
        });
    }
};
