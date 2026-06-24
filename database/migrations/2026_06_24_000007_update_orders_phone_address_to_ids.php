<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_phone_id')->nullable()->after('customer_id')->constrained('customer_phones')->nullOnDelete();
            $table->foreignId('customer_address_id')->nullable()->after('customer_phone_id')->constrained('customer_addresses')->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['phone', 'delivery_address']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('customer_id');
            $table->text('delivery_address')->nullable()->after('phone');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_phone_id']);
            $table->dropForeign(['customer_address_id']);
            $table->dropColumn(['customer_phone_id', 'customer_address_id']);
        });
    }
};
