<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('users', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('users', 'ward')) {
                $table->dropColumn('ward');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('province', 10)->nullable()->after('email');
            $table->string('district', 10)->nullable()->after('province');
            $table->string('ward', 10)->nullable()->after('district');
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('customer_addresses', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('customer_addresses', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('customer_addresses', 'ward')) {
                $table->dropColumn('ward');
            }
        });
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->string('province', 10)->nullable()->after('customer_id');
            $table->string('district', 10)->nullable()->after('province');
            $table->string('ward', 10)->nullable()->after('district');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('users', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('users', 'ward')) {
                $table->dropColumn('ward');
            }
            $table->bigInteger('province')->nullable();
            $table->bigInteger('district')->nullable();
            $table->bigInteger('ward')->nullable();
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('customer_addresses', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('customer_addresses', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('customer_addresses', 'ward')) {
                $table->dropColumn('ward');
            }
            $table->bigInteger('province')->nullable();
            $table->bigInteger('district')->nullable();
            $table->bigInteger('ward')->nullable();
        });
    }
};
