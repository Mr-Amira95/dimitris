<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->nullable();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('phone');
            $table->text('delivery_address');
            $table->date('order_date');
            $table->date('preferred_delivery_date')->nullable();
            $table->time('preferred_delivery_time')->nullable();
            $table->text('internal_notes')->nullable();
            $table->enum('status', ['new', 'packing', 'dispatch', 'picked_up', 'delivered', 'cancelled'])->default('new');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('filling_id')->constrained()->restrictOnDelete();
            $table->string('grind');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->timestamps();
        });

        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('action');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
