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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();            
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->string('seed_supplier')->nullable();
            $table->decimal('cultivated_area', 8, 2)->nullable();
            $table->dateTime('sown_at')->nullable();
            $table->dateTime('harvested_at')->nullable();
            $table->enum('pricing_type', ['fix', 'flexible', 'contact']);
            $table->decimal('base_price', 10, 2);
            $table->decimal('stock_quantity', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->decimal('sold_quantity', 10, 2)->default(0);
            $table->unsignedBigInteger('views')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keyword')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('product_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
