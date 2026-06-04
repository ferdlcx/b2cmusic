<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('district', 100)->nullable()->after('province_id'); // kecamatan
            $table->string('village', 100)->nullable()->after('district'); // kelurahan
            $table->decimal('latitude', 10, 8)->nullable()->after('village');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['district', 'village', 'latitude', 'longitude']);
        });
    }
};
