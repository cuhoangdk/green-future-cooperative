<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_histories', function (Blueprint $table) {
            // Xóa ràng buộc khóa ngoại và cột changed_by
            $table->dropForeign(['changed_by']);
            $table->dropColumn('changed_by');

            // Thêm cột đa hình
            $table->nullableMorphs('changeable');
        });
    }

    public function down(): void
    {
        Schema::table('order_histories', function (Blueprint $table) {
            // Khôi phục cột changed_by và ràng buộc khóa ngoại
            $table->dropMorphs('changeable');
            $table->bigInteger('changed_by')->unsigned()->nullable()->index();
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
