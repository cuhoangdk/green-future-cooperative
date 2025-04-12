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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('order_code')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'delivered', 'cancelled'])->default('pending');
            $table->string('full_name');
            $table->string('phone_number');
            $table->enum('address_type', ['home', 'work', 'other'])->default('home');
            $table->string('province', 10);
            $table->string('district', 10);
            $table->string('ward', 10);
            $table->text('street_address');
            $table->decimal('total_price', 15, 2);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('final_total_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
