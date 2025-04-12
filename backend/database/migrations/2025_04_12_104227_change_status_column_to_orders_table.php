<?php

use App\Models\Order;
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
        // Bước 1: Đổi cột status thành VARCHAR tạm thời
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        // Bước 2: Ánh xạ dữ liệu
        Order::where('status', 'processing')->update(['status' => 'delivering']);

        // Bước 3: Đổi lại thành ENUM mới
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'delivering', 'delivered', 'cancelled', 'return'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bước 1: Đổi thành VARCHAR tạm thời
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        // Bước 2: Ánh xạ ngược
        Order::whereIn('status', ['delivering', 'return'])->update(['status' => 'processing']);

        // Bước 3: Đổi lại ENUM cũ
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'delivered', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }
};
