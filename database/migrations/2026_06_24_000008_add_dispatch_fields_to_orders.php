<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('driver_user_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->string('outsourced_driver_name')->nullable()->after('driver_user_id');
            $table->decimal('outsourced_delivery_cost', 10, 3)->nullable()->after('outsourced_driver_name');
            $table->timestamp('dispatched_at')->nullable()->after('outsourced_delivery_cost');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_user_id']);
            $table->dropColumn(['driver_user_id', 'outsourced_driver_name', 'outsourced_delivery_cost', 'dispatched_at']);
        });
    }
};
