<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn(Blueprint $t) => $t->softDeletes());
        Schema::table('products', fn(Blueprint $t) => $t->softDeletes());
        Schema::table('fillings', fn(Blueprint $t) => $t->softDeletes());
        Schema::table('customers', fn(Blueprint $t) => $t->softDeletes());
        Schema::table('customer_phones', fn(Blueprint $t) => $t->softDeletes());
        Schema::table('customer_addresses', fn(Blueprint $t) => $t->softDeletes());
    }

    public function down(): void
    {
        Schema::table('users', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('products', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('fillings', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('customers', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('customer_phones', fn(Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('customer_addresses', fn(Blueprint $t) => $t->dropSoftDeletes());
    }
};
