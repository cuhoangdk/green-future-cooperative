<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Chạy migration để xóa cột.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_account_number', 'bank_name']);
        });
    }

    /**
     * Rollback migration nếu cần.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_account_number', 20)->nullable();
            $table->string('bank_name')->nullable();
        });
    }
};
