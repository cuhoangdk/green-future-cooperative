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
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Tên tham số, không trùng lặp
            $table->string('value'); // Giá trị tham số
            $table->timestamps();
        });
        DB::table('parameters')->insert([
            'id' => 1,
            'name' => 'shipping_fee',
            'value' => '5000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
