<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        
            // Bước 1: Lấy danh sách khóa ngoại liên quan đến orders.id và products.id
            $database = DB::getDatabaseName();
            $foreignKeys = DB::select("
                SELECT 
                    TABLE_NAME, 
                    COLUMN_NAME, 
                    CONSTRAINT_NAME 
                FROM 
                    information_schema.key_column_usage 
                WHERE 
                    REFERENCED_TABLE_NAME IN ('orders', 'products') 
                    AND REFERENCED_COLUMN_NAME = 'id' 
                    AND TABLE_SCHEMA = ?
            ", [$database]);

            \Log::info('Foreign keys for orders.id and products.id: ' . json_encode($foreignKeys));

            // Bước 2: Xóa các khóa ngoại
            foreach ($foreignKeys as $fk) {
                try {
                    Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                        $table->dropForeign($fk->CONSTRAINT_NAME);
                    });
                    \Log::info("Dropped foreign key {$fk->CONSTRAINT_NAME} on {$fk->TABLE_NAME}.{$fk->COLUMN_NAME}");
                } catch (\Exception $e) {
                    \Log::warning("Failed to drop foreign key {$fk->CONSTRAINT_NAME}: " . $e->getMessage());
                }
            }

            // Bước 3: Xóa dữ liệu trong các bảng liên quan
            $relatedTables = ['order_items', 'cart_items', 'orders', 'products', 'product_images', 'product_quantity_prices', 'cultivation_logs'];
            foreach ($relatedTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    \Log::info("Truncated table {$table}");
                }
            }

            // Bước 4: Cập nhật cấu trúc bảng orders
            Schema::table('orders', function (Blueprint $table) {
                // Bỏ thuộc tính AUTO_INCREMENT
                DB::statement('ALTER TABLE orders MODIFY id BIGINT NOT NULL');
                \Log::info('Removed AUTO_INCREMENT from orders.id');
                // Xóa khóa chính cũ
                $table->dropPrimary('id');
                // Thay đổi id thành string(50)
                $table->string('id', 50)->change();
                // Thêm khóa chính mới
                $table->primary('id');
                // Xóa cột order_code nếu tồn tại
                if (Schema::hasColumn('orders', 'order_code')) {
                    $table->dropColumn('order_code');
                }
                // Xóa cột temp_id nếu tồn tại
                if (Schema::hasColumn('orders', 'temp_id')) {
                    $table->dropColumn('temp_id');
                }
            });
            \Log::info('Updated orders table: id to string(50), dropped order_code, temp_id');

            // Bước 5: Cập nhật cấu trúc bảng products
            Schema::table('products', function (Blueprint $table) {
                // Bỏ thuộc tính AUTO_INCREMENT
                DB::statement('ALTER TABLE products MODIFY id BIGINT NOT NULL');
                \Log::info('Removed AUTO_INCREMENT from products.id');
                // Xóa khóa chính cũ
                $table->dropPrimary('id');
                // Thay đổi id thành string(255)
                $table->string('id', 255)->change();
                // Thêm khóa chính mới
                $table->primary('id');
                // Xóa cột product_code nếu tồn tại
                if (Schema::hasColumn('products', 'product_code')) {
                    $table->dropColumn('product_code');
                }
                // Xóa cột temp_id nếu tồn tại
                if (Schema::hasColumn('products', 'temp_id')) {
                    $table->dropColumn('temp_id');
                }
            });
            \Log::info('Updated products table: id to string(255), dropped product_code, temp_id');

            // Bước 6: Cập nhật kiểu dữ liệu khóa ngoại và tái tạo khóa ngoại
            foreach ($foreignKeys as $fk) {
                Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                    // Thay đổi kiểu dữ liệu khóa ngoại
                    $length = $fk->REFERENCED_TABLE_NAME === 'orders' ? 50 : 255;
                    $table->string($fk->COLUMN_NAME, $length)->change();
                    // Tái tạo khóa ngoại
                    $table->foreign($fk->COLUMN_NAME)
                          ->references('id')
                          ->on($fk->REFERENCED_TABLE_NAME)
                          ->onUpdate('cascade')
                          ->onDelete('cascade');
                });
                \Log::info("Updated {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} to string and recreated foreign key");
            }
        
    }

    public function down()
    {
        
            // Bước 1: Lấy danh sách khóa ngoại
            $database = DB::getDatabaseName();
            $foreignKeys = DB::select("
                SELECT 
                    TABLE_NAME, 
                    COLUMN_NAME, 
                    CONSTRAINT_NAME 
                FROM 
                    information_schema.key_column_usage 
                WHERE 
                    REFERENCED_TABLE_NAME IN ('orders', 'products') 
                    AND REFERENCED_COLUMN_NAME = 'id' 
                    AND TABLE_SCHEMA = ?
            ", [$database]);

            \Log::info('Foreign keys for orders.id and products.id in down: ' . json_encode($foreignKeys));

            // Bước 2: Xóa các khóa ngoại
            foreach ($foreignKeys as $fk) {
                try {
                    Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                        $table->dropForeign($fk->CONSTRAINT_NAME);
                    });
                    \Log::info("Dropped foreign key {$fk->CONSTRAINT_NAME} in down");
                } catch (\Exception $e) {
                    \Log::warning("Failed to drop foreign key {$fk->CONSTRAINT_NAME} in down: " . $e->getMessage());
                }
            }

            // Bước 3: Khôi phục cấu trúc bảng orders
            Schema::table('orders', function (Blueprint $table) use ($database) {
                // Xóa khóa chính
                $table->dropPrimary('id');
                \Log::info('Dropped primary key on orders.id');
                // Thay đổi id về unsignedBigInteger
                $table->unsignedBigInteger('id')->change();
                \Log::info('Changed orders.id to unsignedBigInteger');
                // Thêm khóa chính
                $table->primary('id');
                \Log::info('Added primary key on orders.id');
                // Thêm lại cột order_code
                $table->string('order_code', 50)->unique()->after('id');
                \Log::info('Added order_code column to orders');
            });
            // Thêm AUTO_INCREMENT sau khi id là khóa chính
            DB::statement('ALTER TABLE orders MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            \Log::info('Added AUTO_INCREMENT to orders.id');

            // Bước 4: Khôi phục cấu trúc bảng products
            Schema::table('products', function (Blueprint $table) use ($database) {
                // Xóa khóa chính
                $table->dropPrimary('id');
                \Log::info('Dropped primary key on products.id');
                // Thay đổi id về unsignedBigInteger
                $table->unsignedBigInteger('id')->change();
                \Log::info('Changed products.id to unsignedBigInteger');
                // Thêm khóa chính
                $table->primary('id');
                \Log::info('Added primary key on products.id');
                // Thêm lại cột product_code
                $table->string('product_code', 255)->unique()->after('id');
                \Log::info('Added product_code column to products');
            });
            // Thêm AUTO_INCREMENT sau khi id là khóa chính
            DB::statement('ALTER TABLE products MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            \Log::info('Added AUTO_INCREMENT to products.id');

            // Bước 5: Khôi phục kiểu dữ liệu khóa ngoại và tái tạo khóa ngoại
            foreach ($foreignKeys as $fk) {
                Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                    // Thay đổi kiểu dữ liệu khóa ngoại về unsignedBigInteger
                    $table->unsignedBigInteger($fk->COLUMN_NAME)->change();
                    \Log::info("Changed {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} to unsignedBigInteger");
                    // Tái tạo khóa ngoại
                    $table->foreign($fk->COLUMN_NAME)
                          ->references('id')
                          ->on($fk->REFERENCED_TABLE_NAME)
                          ->onUpdate('cascade')
                          ->onDelete('cascade');
                    \Log::info("Recreated foreign key on {$fk->TABLE_NAME}.{$fk->COLUMN_NAME}");
                });
            }
        
    }
};