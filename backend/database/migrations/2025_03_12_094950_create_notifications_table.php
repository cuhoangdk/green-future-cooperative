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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('user_type'); // 'member' (api_users) hoặc 'customer' (api_customers)
            $table->unsignedBigInteger('user_id')->nullable(); // Có thể null nếu thông báo hệ thống không gắn với user cụ thể
            $table->string('title');
            $table->string('type'); // 'order_status', 'system', v.v.            
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Index để tối ưu truy vấn
            $table->index(['user_id', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
