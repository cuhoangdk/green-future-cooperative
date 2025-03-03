<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductQuantityPricesTable extends Migration
{
    public function up()
    {
        Schema::create('product_quantity_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 8, 2); // Số lượng (kg), 8 chữ số, 2 sau dấu chấm
            $table->decimal('price', 15, 2); // Giá, hỗ trợ số lớn (15 chữ số, 2 sau dấu chấm)
            $table->timestamps();
            $table->softDeletes();

            // Đảm bảo product_id và quantity là cặp duy nhất
            $table->unique(['product_id', 'quantity']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_quantity_prices');
    }
}