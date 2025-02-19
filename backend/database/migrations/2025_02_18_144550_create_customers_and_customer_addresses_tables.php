<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersAndCustomerAddressesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tạo bảng customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('phone_number', 20)->unique();
            $table->string('gender')->nullable();
            
            // Thông tin mua sắm
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spending', 10, 2)->default(0);
            $table->timestamp('last_order_date')->nullable();

            // Thông tin khác
            $table->boolean('newsletter_subscribed')->default(false);

            // Thông tin xác thực
            $table->boolean('is_banned')->default(false);
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Tạo bảng customer_addresses
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // Thông tin địa chỉ
            $table->string('full_name');
            $table->string('phone_number', 20);
            $table->enum('address_type', ['home', 'work', 'other'])->default('home');
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->text('street_address');
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
}