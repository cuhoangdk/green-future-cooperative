<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Đổi tên cột author_id thành user_id
            $table->renameColumn('author_id', 'user_id');

            // Thêm các cột mới
            $table->integer('hot_order')->nullable()->after('is_hot');
            $table->integer('featured_order')->nullable()->after('is_featured');
            $table->text('tags')->nullable()->after('featured_order');
            $table->text('meta_title')->nullable()->after('tags');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->integer('views')->default(0)->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Đổi lại tên cột user_id về author_id
            $table->renameColumn('user_id', 'author_id');

            // Xóa các cột đã thêm
            $table->dropColumn('hot_order');
            $table->dropColumn('featured_order');
            $table->dropColumn('tags');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('views');
        });
    }
}
