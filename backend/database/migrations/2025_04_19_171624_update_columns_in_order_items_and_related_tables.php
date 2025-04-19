<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Sửa đổi cột order_id trong bảng order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('order_id', 50)->change();  // Chuyển cột order_id thành varchar(50)
            $table->string('product_id', 255)->change();  // Chuyển cột product_id thành varchar(255)
        });

        // Sửa đổi cột product_id trong bảng cultivation_logs
        Schema::table('cultivation_logs', function (Blueprint $table) {
            $table->string('product_id', 255)->change();  // Chuyển cột product_id thành varchar(255)
        });

        // Sửa đổi cột product_id trong bảng product_images
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('product_id', 255)->change();  // Chuyển cột product_id thành varchar(255)
        });

        // Sửa đổi cột product_id trong bảng product_quantity_prices
        Schema::table('product_quantity_prices', function (Blueprint $table) {
            $table->string('product_id', 255)->change();  // Chuyển cột product_id thành varchar(255)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nếu rollback, khôi phục lại các kiểu dữ liệu cũ
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('order_id')->change();  // Khôi phục lại order_id thành integer (hoặc kiểu ban đầu)
            $table->integer('product_id')->change();  // Khôi phục lại product_id thành integer (hoặc kiểu ban đầu)
        });

        Schema::table('cultivation_logs', function (Blueprint $table) {
            $table->integer('product_id')->change();  // Khôi phục lại product_id thành integer (hoặc kiểu ban đầu)
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->integer('product_id')->change();  // Khôi phục lại product_id thành integer (hoặc kiểu ban đầu)
        });

        Schema::table('product_quantity_prices', function (Blueprint $table) {
            $table->integer('product_id')->change();  // Khôi phục lại product_id thành integer (hoặc kiểu ban đầu)
        });
    }
};
