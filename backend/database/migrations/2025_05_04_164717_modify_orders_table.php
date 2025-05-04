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
            $table->string('full_name')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('ward')->nullable()->change();
            $table->string('street_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('full_name')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->string('province')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('ward')->nullable(false)->change();
            $table->string('street_address')->nullable(false)->change();
        });
    }
};
