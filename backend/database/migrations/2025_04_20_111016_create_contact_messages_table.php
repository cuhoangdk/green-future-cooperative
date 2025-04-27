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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id(); // Khóa chính, tự động tăng (BIGINT)
            $table->string('name'); // Cột name, VARCHAR(255), NOT NULL
            $table->string('email'); // Cột email, VARCHAR(255), NOT NULL
            $table->string('phone', 20); // Cột phone, VARCHAR(20), có thể để trống
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); // Cột gender, ENUM, có thể để trống
            $table->text('message'); // Cột message, TEXT, NOT NULL
            $table->timestamps(); // Tạo cột created_at và updated_at

            // Thêm index cho cột email
            $table->index('email', 'idx_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
