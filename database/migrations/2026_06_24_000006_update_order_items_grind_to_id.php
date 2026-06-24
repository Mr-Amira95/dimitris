<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('grind_id')->nullable()->after('filling_id')->constrained('grinds')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('grind');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('grind')->nullable()->after('filling_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['grind_id']);
            $table->dropColumn('grind_id');
        });
    }
};
