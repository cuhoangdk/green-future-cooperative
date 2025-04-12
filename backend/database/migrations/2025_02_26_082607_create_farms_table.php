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
        Schema::create('farms', function (Blueprint $table) {
            $table->id()->from(100);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');

            // Địa chỉ
            $table->string('province', 10)->nullable();
            $table->string('district', 10)->nullable();
            $table->string('ward', 10)->nullable();
            $table->text('street_address')->nullable();

            // Thông tin thêm
            $table->text('description')->nullable();
            $table->decimal('farm_size', 10, 2)->nullable();
            $table->string('soil_type')->nullable();
            $table->string('irrigation_method')->nullable();

            // Tọa độ
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
            $table->softDeletes();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
