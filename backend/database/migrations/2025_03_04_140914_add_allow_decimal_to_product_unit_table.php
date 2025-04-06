<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowDecimalToProductUnitTable extends Migration
{
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->boolean('allow_decimal')->default(true); // Mặc định cho phép mua lẻ
        });
    }

    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn('allow_decimal');
        });
    }
}