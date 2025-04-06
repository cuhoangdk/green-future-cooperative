<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward']); // Xoá cột cũ
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('province')->nullable()->after('email');
            $table->unsignedBigInteger('district')->nullable()->after('province');
            $table->unsignedBigInteger('ward')->nullable()->after('district');
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward']); // Xoá cột cũ
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('province')->nullable()->after('customer_id');
            $table->unsignedBigInteger('district')->nullable()->after('province');
            $table->unsignedBigInteger('ward')->nullable()->after('district');
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward']);
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'district', 'ward']);
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
        });
    }
};

