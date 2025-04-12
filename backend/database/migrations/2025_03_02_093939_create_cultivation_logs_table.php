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
        Schema::create('cultivation_logs', function (Blueprint $table) {
            $table->id()->from(100);
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('activity'); // Hoạt động canh tác
            $table->string('fertilizer_used')->nullable(); // Phân bón sử dụng
            $table->string('pesticide_used')->nullable(); // Thuốc trừ sâu sử dụng
            $table->string('image_url')->nullable(); // URL hình ảnh
            $table->string('video_url')->nullable(); // URL video
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivation_logs');
    }
};
