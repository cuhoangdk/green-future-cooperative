<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsReplaceIsActiveWithStatus extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Xóa cột is_active
            $table->dropColumn('is_active');

            // Thêm cột status với ENUM
            $table->enum('status', ['growing', 'selling', 'stopped'])->default('growing');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Khôi phục cột is_active
            $table->boolean('is_active')->default(true);

            // Xóa cột status
            $table->dropColumn('status');
        });
    }
}