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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward', 'street_address']);
        });
        
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward', 'street_address']);
        });
        
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward', 'street_address']);
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('users', function (Blueprint $table) {            
            $table->string('province', 10)->nullable();
            $table->string('district', 10)->nullable();
            $table->string('ward', 10)->nullable();
            $table->text('street_address')->nullable();
        });
        Schema::create('farms', function (Blueprint $table) {            
            $table->string('province', 10)->nullable();
            $table->string('district', 10)->nullable();
            $table->string('ward', 10)->nullable();
            $table->text('street_address')->nullable();
        });
        Schema::create('customer_addresses', function (Blueprint $table) {            
            $table->string('province', 10)->nullable();
            $table->string('district', 10)->nullable();
            $table->string('ward', 10)->nullable();
            $table->text('street_address')->nullable();
        });
    }
};
