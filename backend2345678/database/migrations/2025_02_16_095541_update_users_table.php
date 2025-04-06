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
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone_number');
            // Thêm cột mới            
            $table->string('usercode')->index();            
            $table->boolean('is_super_admin')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->text('street_address')->nullable();
            $table->softDeletes();

            // Xóa các cột cũ
            $table->dropColumn([
                'username',
                'slug',
                'address',
                'verified_at',
                'joined_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_number']);
            // Khôi phục các cột cũ
            $table->string('username')->nullable();
            $table->string('slug')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('joined_at')->nullable();

            // Xóa các cột mới
            $table->dropColumn([
                'usercode',                
                'is_super_admin',
                'is_banned',
                'province',
                'district',
                'ward',
                'street_address',
                'deleted_at'
            ]);
        });
    }
};
